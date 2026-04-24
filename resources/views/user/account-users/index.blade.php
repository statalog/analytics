@extends('layouts.app')
@section('title', 'Team members')
@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <h4 class="mb-0 font-heading-bold">
        <i class="bi bi-people me-2 icon-primary"></i>Team members
    </h4>
</div>

<p style="color:var(--pa-text-muted);max-width:720px;margin-bottom:1.5rem">
    Invite people to your analytics by email. <strong>Admins</strong> can manage everything. <strong>Viewers</strong> see reports only.
</p>


<div class="row g-4" style="max-width:1100px">

    {{-- Invite form --}}
    <div class="col-md-4">
        <div class="pa-card">
            <h6 class="mb-3 font-heading-bold">Invite by email</h6>
            <form method="POST" action="{{ route('user.invitations.store') }}" id="invite-form">
                @csrf
                <div class="mb-3">
                    <label class="auth-label">Email address</label>
                    <input type="email" name="email" class="pa-input @error('email') is-invalid @enderror"
                           required value="{{ old('email') }}" placeholder="person@example.com" autocomplete="off">
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="auth-label">Role</label>
                    <select name="role" id="role-select" class="pa-input" required onchange="toggleSiteSelector(this.value)">
                        <option value="viewer" {{ old('role','viewer')==='viewer'?'selected':'' }}>Viewer — read-only</option>
                        <option value="admin"  {{ old('role')==='admin'?'selected':'' }}>Admin — full access</option>
                    </select>
                </div>

                {{-- Per-site selector (viewers only) --}}
                @if($sites->count())
                <div id="site-selector" style="display:none;margin-bottom:1rem">
                    <label class="auth-label">Site access</label>
                    <div style="background:var(--pa-input-bg);border:1px solid var(--pa-border);border-radius:8px;padding:0.5rem 0.75rem;max-height:180px;overflow-y:auto">
                        <label style="display:flex;align-items:center;gap:0.5rem;font-size:0.875rem;padding:0.25rem 0;cursor:pointer;color:var(--pa-text-muted)">
                            <input type="checkbox" id="all-sites-cb" checked onchange="toggleAllSites(this.checked)" style="accent-color:var(--pa-primary)">
                            All sites
                        </label>
                        <hr style="margin:0.25rem 0;border-color:var(--pa-border)">
                        @foreach($sites as $site)
                        <label style="display:flex;align-items:center;gap:0.5rem;font-size:0.875rem;padding:0.25rem 0;cursor:pointer">
                            <input type="checkbox" name="site_ids[]" value="{{ $site->id }}" class="site-cb" style="accent-color:var(--pa-primary)">
                            {{ $site->name }}
                        </label>
                        @endforeach
                    </div>
                    <div style="font-size:0.75rem;color:var(--pa-text-muted);margin-top:0.35rem">Leave "All sites" checked to grant access to everything.</div>
                </div>
                @endif

                <button type="submit" class="btn-pa-primary w-100"><i class="bi bi-send me-1"></i>Send invitation</button>
            </form>
        </div>
    </div>

    <div class="col-md-8">

        {{-- Pending invitations --}}
        @if($invitations->count())
        <div class="pa-card p-0 mb-4">
            <div style="padding:0.875rem 1.25rem;border-bottom:1px solid var(--pa-border);display:flex;align-items:center;justify-content:space-between">
                <h6 class="mb-0 font-heading-bold">Pending invitations</h6>
                <span class="badge" style="background:var(--pa-input-bg);color:var(--pa-text-muted);font-size:0.75rem;border-radius:20px;padding:0.2rem 0.6rem">{{ $invitations->count() }}</span>
            </div>
            <table class="pa-table">
                <thead>
                    <tr><th>Email</th><th>Role</th><th>Status</th><th>Expires</th><th></th></tr>
                </thead>
                <tbody>
                    @foreach($invitations as $inv)
                    <tr>
                        <td class="fw-medium">{{ $inv->email }}</td>
                        <td class="text-sm">{{ ucfirst($inv->role) }}</td>
                        <td class="text-sm">
                            @if($inv->accepted_at)
                                <span style="color:#22c55e"><i class="bi bi-check-circle-fill me-1"></i>Accepted {{ $inv->accepted_at->format('M j') }}</span>
                            @elseif($inv->opened_at)
                                <span class="icon-primary"><i class="bi bi-envelope-open me-1"></i>Opened {{ $inv->opened_at->diffForHumans() }}</span>
                            @else
                                <span class="text-muted"><i class="bi bi-envelope me-1"></i>Not opened</span>
                            @endif
                        </td>
                        <td class="text-sm-muted">{{ $inv->expires_at->format('M j') }}</td>
                        <td class="text-end">
                            <form method="POST" action="{{ route('user.invitations.destroy', $inv) }}" class="d-inline delete-form">
                                @csrf @method('DELETE')
                                <button type="button" class="btn-pa-outline" style="padding:0.2rem 0.5rem;font-size:0.8125rem;color:var(--pa-danger)"
                                        onclick="confirmDelete(this.closest('form'), 'Revoke invitation?', 'This will revoke the invitation sent to <strong>{{ $inv->email }}</strong>. They will no longer be able to use the link.', 'Revoke invitation')">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        {{-- Active members --}}
        <div class="pa-card p-0">
            <div style="padding:0.875rem 1.25rem;border-bottom:1px solid var(--pa-border)">
                <h6 class="mb-0 font-heading-bold">Active members</h6>
            </div>
            @if($members->isEmpty())
                <div class="text-center py-5">
                    <i class="bi bi-people" style="font-size:2rem;color:var(--pa-primary);opacity:.4"></i>
                    <div style="color:var(--pa-text-muted);margin-top:0.75rem">Nobody else has access yet.</div>
                </div>
            @else
                <table class="pa-table">
                    <thead>
                        <tr><th>User</th><th>Role</th><th>Site access</th><th></th></tr>
                    </thead>
                    <tbody>
                        @foreach($members as $m)
                        <tr>
                            <td>
                                <div class="fw-medium">{{ $m->user?->name ?? '—' }}</div>
                                <div class="text-xs-muted">{{ $m->user?->email }}</div>
                            </td>
                            <td>
                                <form method="POST" action="{{ route('user.account-users.update', $m) }}" class="d-inline">
                                    @csrf @method('PUT')
                                    <select name="role" class="pa-input" style="padding:0.25rem 0.5rem;font-size:0.8125rem" onchange="this.form.submit()">
                                        <option value="viewer" {{ $m->role === 'viewer' ? 'selected' : '' }}>Viewer</option>
                                        <option value="admin"  {{ $m->role === 'admin'  ? 'selected' : '' }}>Admin</option>
                                    </select>
                                </form>
                            </td>
                            <td class="text-sm-muted">
                                @if($m->isAdmin() || $m->siteAccess->count() === 0)
                                    All sites
                                @else
                                    {{ $m->siteAccess->count() }} / {{ $sites->count() }} sites
                                @endif
                            </td>
                            <td class="text-end">
                                <form method="POST" action="{{ route('user.account-users.destroy', $m) }}" class="d-inline delete-form">
                                    @csrf @method('DELETE')
                                    <button type="button" class="btn-pa-outline" style="padding:0.2rem 0.5rem;font-size:0.8125rem;color:var(--pa-danger)"
                                            onclick="confirmDelete(this.closest('form'), 'Remove member?', 'This will remove <strong>{{ $m->user?->name ?? $m->user?->email }}</strong> from your account. They will lose all access immediately.', 'Remove member')">
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

    </div>
</div>
@endsection

{{-- Confirmation modal --}}
<div id="delete-modal" style="display:none;position:fixed;inset:0;z-index:1050;background:rgba(0,0,0,0.45);align-items:center;justify-content:center">
    <div style="background:var(--pa-card-bg);border:1px solid var(--pa-border);border-radius:1rem;padding:2rem;max-width:420px;width:90%;box-shadow:0 20px 60px rgba(0,0,0,0.2)">
        <h6 id="delete-modal-title" style="font-family:'Space Grotesk',sans-serif;font-weight:700;margin-bottom:0.75rem"></h6>
        <p id="delete-modal-body" style="color:var(--pa-text-muted);font-size:0.9375rem;margin-bottom:1.5rem;line-height:1.6"></p>
        <div style="display:flex;gap:0.75rem;justify-content:flex-end">
            <button type="button" class="btn-pa-outline" onclick="closeDeleteModal()">Cancel</button>
            <button type="button" id="delete-modal-confirm" class="btn-pa-primary" style="background:var(--pa-danger);border-color:var(--pa-danger)"></button>
        </div>
    </div>
</div>

@push('scripts')
<script>
var _deleteForm = null;

function confirmDelete(form, title, body, btnLabel) {
    _deleteForm = form;
    document.getElementById('delete-modal-title').textContent = title;
    document.getElementById('delete-modal-body').innerHTML = body;
    document.getElementById('delete-modal-confirm').textContent = btnLabel;
    var modal = document.getElementById('delete-modal');
    modal.style.display = 'flex';
}

function closeDeleteModal() {
    document.getElementById('delete-modal').style.display = 'none';
    _deleteForm = null;
}

document.getElementById('delete-modal-confirm').addEventListener('click', function() {
    if (_deleteForm) _deleteForm.submit();
});

document.getElementById('delete-modal').addEventListener('click', function(e) {
    if (e.target === this) closeDeleteModal();
});

function toggleSiteSelector(role) {
    var el = document.getElementById('site-selector');
    if (el) el.style.display = role === 'viewer' ? 'block' : 'none';
}
function toggleAllSites(checked) {
    document.querySelectorAll('.site-cb').forEach(function(cb) { cb.checked = false; cb.disabled = checked; });
}

// Init
(function() {
    var role = document.getElementById('role-select');
    if (role) toggleSiteSelector(role.value);
    var allCb = document.getElementById('all-sites-cb');
    if (allCb) toggleAllSites(allCb.checked);

    // If no specific sites checked, keep all-sites checked on submit
    document.getElementById('invite-form').addEventListener('submit', function() {
        var allCb = document.getElementById('all-sites-cb');
        if (!allCb || allCb.checked) {
            document.querySelectorAll('.site-cb').forEach(function(cb) { cb.disabled = true; });
        }
    });
})();
</script>
@endpush
