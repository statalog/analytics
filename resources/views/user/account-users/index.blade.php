@extends('layouts.app')
@section('title', 'Team members')
@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <h4 class="mb-0" style="font-family:'Space Grotesk',sans-serif;font-weight:700">
        <i class="bi bi-people me-2" style="color:var(--pa-primary)"></i>Team members
    </h4>
</div>

<p style="color:var(--pa-text-muted);max-width:720px;margin-bottom:1.5rem">
    Invite people to your analytics by email. <strong>Admins</strong> can manage everything. <strong>Viewers</strong> see reports only.
</p>


<div class="row g-4" style="max-width:1100px">

    {{-- Invite form --}}
    <div class="col-md-4">
        <div class="pa-card">
            <h6 class="mb-3" style="font-family:'Space Grotesk',sans-serif;font-weight:700">Invite by email</h6>
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
                <h6 class="mb-0" style="font-family:'Space Grotesk',sans-serif;font-weight:700">Pending invitations</h6>
                <span class="badge" style="background:var(--pa-input-bg);color:var(--pa-text-muted);font-size:0.75rem;border-radius:20px;padding:0.2rem 0.6rem">{{ $invitations->count() }}</span>
            </div>
            <table class="pa-table">
                <thead>
                    <tr><th>Email</th><th>Role</th><th>Status</th><th>Expires</th><th></th></tr>
                </thead>
                <tbody>
                    @foreach($invitations as $inv)
                    <tr>
                        <td style="font-weight:500">{{ $inv->email }}</td>
                        <td style="font-size:0.8125rem">{{ ucfirst($inv->role) }}</td>
                        <td style="font-size:0.8125rem">
                            @if($inv->accepted_at)
                                <span style="color:#22c55e"><i class="bi bi-check-circle-fill me-1"></i>Accepted {{ $inv->accepted_at->format('M j') }}</span>
                            @elseif($inv->opened_at)
                                <span style="color:var(--pa-primary)"><i class="bi bi-envelope-open me-1"></i>Opened {{ $inv->opened_at->diffForHumans() }}</span>
                            @else
                                <span style="color:var(--pa-text-muted)"><i class="bi bi-envelope me-1"></i>Not opened</span>
                            @endif
                        </td>
                        <td style="font-size:0.8125rem;color:var(--pa-text-muted)">{{ $inv->expires_at->format('M j') }}</td>
                        <td class="text-end">
                            <form method="POST" action="{{ route('user.invitations.destroy', $inv) }}" class="d-inline"
                                  onsubmit="return confirm('Revoke invitation for {{ $inv->email }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-pa-outline" style="padding:0.2rem 0.5rem;font-size:0.8125rem;color:var(--pa-danger)">
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
                <h6 class="mb-0" style="font-family:'Space Grotesk',sans-serif;font-weight:700">Active members</h6>
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
                                <div style="font-weight:500">{{ $m->user?->name ?? '—' }}</div>
                                <div style="font-size:0.75rem;color:var(--pa-text-muted)">{{ $m->user?->email }}</div>
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
                            <td style="font-size:0.8125rem;color:var(--pa-text-muted)">
                                @if($m->isAdmin() || $m->siteAccess->count() === 0)
                                    All sites
                                @else
                                    {{ $m->siteAccess->count() }} / {{ $sites->count() }} sites
                                @endif
                            </td>
                            <td class="text-end">
                                <form method="POST" action="{{ route('user.account-users.destroy', $m) }}" class="d-inline"
                                      onsubmit="return confirm('Remove {{ $m->user?->email }}\'s access?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-pa-outline" style="padding:0.2rem 0.5rem;font-size:0.8125rem;color:var(--pa-danger)">
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

@push('scripts')
<script>
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
