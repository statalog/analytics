@extends('layouts.app')
@section('title', 'Team members')
@section('content')
<div class="d-flex align-items-center justify-content-between mb-3">
    <h4 class="mb-0 font-heading-bold">
        <i class="bi bi-people me-2 icon-primary"></i>Team members
    </h4>
</div>

<div class="pa-card mb-4" style="background:var(--pa-input-bg);border-color:var(--pa-border)">
    <div class="row g-3 align-items-start">
        <div class="col-md-8">
            <div style="font-weight:600;margin-bottom:0.375rem">Invite people to your analytics workspace</div>
            <div style="color:var(--pa-text-muted);font-size:0.875rem;line-height:1.65">
                Send an invitation by email — the recipient will receive a link they must click to accept.
                If they don't have a Statalog account yet, they'll be prompted to create one first.
                Once accepted, they appear under <strong>Active members</strong> below.
            </div>
        </div>
        <div class="col-md-4">
            <div style="display:flex;flex-direction:column;gap:0.5rem">
                <div style="font-size:0.8125rem;display:flex;align-items:flex-start;gap:0.5rem">
                    <i class="bi bi-person-fill-gear icon-primary" style="margin-top:0.1rem;flex-shrink:0"></i>
                    <span><strong>Admin</strong> — full access, can manage sites, members, and settings</span>
                </div>
                <div style="font-size:0.8125rem;display:flex;align-items:flex-start;gap:0.5rem">
                    <i class="bi bi-eye-fill" style="color:var(--pa-text-muted);margin-top:0.1rem;flex-shrink:0"></i>
                    <span><strong>Viewer</strong> — read-only access to reports and dashboards</span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">

    {{-- Invite form --}}
    <div class="col-md-4 col-lg-3">
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
                    <div style="background:var(--pa-input-bg);border:1px solid var(--pa-border);border-radius:8px;overflow:hidden">
                        {{-- All sites row --}}
                        <label style="display:flex;align-items:center;justify-content:space-between;gap:0.75rem;padding:0.6rem 0.875rem;cursor:pointer;border-bottom:1px solid var(--pa-border)">
                            <span style="font-size:0.875rem;color:var(--pa-text-muted)">All sites</span>
                            <span style="position:relative;display:inline-block;width:36px;height:20px;flex-shrink:0">
                                <input type="checkbox" id="all-sites-cb" checked onchange="onAllSitesChange(this.checked)"
                                       style="opacity:0;width:0;height:0;position:absolute">
                                <span class="toggle-track"></span><span class="toggle-dot"></span>
                            </span>
                        </label>
                        {{-- Individual sites — never disabled; clicking one auto-unchecks "All sites" --}}
                        @foreach($sites as $site)
                        <label id="site-row-{{ $site->id }}"
                               style="display:flex;align-items:center;justify-content:space-between;gap:0.75rem;padding:0.6rem 0.875rem;cursor:pointer;opacity:0.4{{ !$loop->last ? ';border-bottom:1px solid var(--pa-border)' : '' }}">
                            <span style="font-size:0.875rem">{{ $site->name }}</span>
                            <span style="position:relative;display:inline-block;width:36px;height:20px;flex-shrink:0">
                                <input type="checkbox" name="site_ids[]" value="{{ $site->id }}" class="site-cb"
                                       onclick="onSiteCbClick()"
                                       style="opacity:0;width:0;height:0;position:absolute">
                                <span class="toggle-track"></span><span class="toggle-dot"></span>
                            </span>
                        </label>
                        @endforeach
                    </div>
                    <div style="font-size:0.75rem;color:var(--pa-text-muted);margin-top:0.35rem">Leave "All sites" on to grant access to everything.</div>
                </div>
                @endif

                <button type="submit" class="btn-pa-primary w-100"><i class="bi bi-send me-1"></i>Send invitation</button>
            </form>
        </div>
    </div>

    <div class="col-md-8 col-lg-9">

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
                                <form method="POST" action="{{ route('user.account-users.update', $m) }}" class="d-inline member-role-form" data-member="{{ $m->id }}">
                                    @csrf @method('PUT')
                                    <input type="hidden" name="sites" value="">{{-- placeholder updated by JS --}}
                                    <select name="role" class="pa-input" style="padding:0.25rem 0.5rem;font-size:0.8125rem" onchange="this.form.submit()">
                                        <option value="viewer" {{ $m->role === 'viewer' ? 'selected' : '' }}>Viewer</option>
                                        <option value="admin"  {{ $m->role === 'admin'  ? 'selected' : '' }}>Admin</option>
                                    </select>
                                </form>
                            </td>
                            <td>
                                @if($m->isAdmin())
                                    <span class="text-sm-muted">All sites</span>
                                @else
                                    <button type="button" class="btn-pa-outline"
                                            style="font-size:0.8125rem;padding:0.2rem 0.6rem"
                                            onclick="openAccessModal({{ $m->id }}, '{{ addslashes($m->user?->name ?? $m->user?->email) }}', {{ json_encode($m->siteAccess->pluck('id')) }})">
                                        @if($m->siteAccess->count() === 0)
                                            All sites <i class="bi bi-pencil ms-1" style="font-size:0.7rem"></i>
                                        @else
                                            {{ $m->siteAccess->count() }} / {{ $sites->count() }} sites <i class="bi bi-pencil ms-1" style="font-size:0.7rem"></i>
                                        @endif
                                    </button>
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

{{-- Site access edit modal --}}
<div id="access-modal-backdrop" class="pa-modal-backdrop" data-pa-modal-backdrop="access-modal"></div>
<div id="access-modal" class="pa-modal" data-pa-modal="access-modal" aria-hidden="true">
    <div class="pa-modal-dialog" style="max-width:420px">
        <div style="padding:1.25rem 1.25rem 0;display:flex;align-items:center;justify-content:space-between">
            <h6 class="mb-0 font-heading-bold">Edit site access — <span id="access-modal-name"></span></h6>
            <button type="button" data-pa-modal-close style="background:none;border:none;font-size:1.25rem;color:var(--pa-text-muted);cursor:pointer;line-height:1;padding:0.25rem">&times;</button>
        </div>
        <form method="POST" id="access-modal-form">
            @csrf @method('PUT')
            <input type="hidden" name="role" value="viewer">
            <div style="padding:1rem 1.25rem">
                <div style="background:var(--pa-input-bg);border:1px solid var(--pa-border);border-radius:8px;overflow:hidden;margin-bottom:0.5rem">
                    {{-- All sites --}}
                    <label style="display:flex;align-items:center;justify-content:space-between;gap:0.75rem;padding:0.6rem 0.875rem;cursor:pointer;border-bottom:1px solid var(--pa-border)">
                        <span style="font-size:0.875rem;color:var(--pa-text-muted)">All sites</span>
                        <span style="position:relative;display:inline-block;width:36px;height:20px;flex-shrink:0">
                            <input type="checkbox" id="modal-all-sites-cb" checked onchange="onModalAllSitesChange(this.checked)"
                                   style="opacity:0;width:0;height:0;position:absolute">
                            <span class="toggle-track"></span><span class="toggle-dot"></span>
                        </span>
                    </label>
                    {{-- Individual sites --}}
                    @foreach($sites as $site)
                    <label id="modal-site-row-{{ $site->id }}"
                           style="display:flex;align-items:center;justify-content:space-between;gap:0.75rem;padding:0.6rem 0.875rem;cursor:pointer;opacity:0.4{{ !$loop->last ? ';border-bottom:1px solid var(--pa-border)' : '' }}">
                        <span style="font-size:0.875rem">{{ $site->name }}</span>
                        <span style="position:relative;display:inline-block;width:36px;height:20px;flex-shrink:0">
                            <input type="checkbox" name="sites[]" value="{{ $site->id }}" class="modal-site-cb"
                                   onclick="onModalSiteCbClick()"
                                   style="opacity:0;width:0;height:0;position:absolute">
                            <span class="toggle-track"></span><span class="toggle-dot"></span>
                        </span>
                    </label>
                    @endforeach
                </div>
                <div style="font-size:0.75rem;color:var(--pa-text-muted)">Leave "All sites" on to grant access to everything.</div>
            </div>
            <div style="padding:0.75rem 1.25rem 1.25rem;display:flex;gap:0.75rem;justify-content:flex-end;border-top:1px solid var(--pa-border)">
                <button type="button" class="btn-pa-outline" data-pa-modal-close>Cancel</button>
                <button type="submit" class="btn-pa-primary">Save</button>
            </div>
        </form>
    </div>
</div>

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
// ── Invite form site selector ──────────────────────────────────────────────
function onAllSitesChange(checked) {
    document.querySelectorAll('.site-cb').forEach(function(cb) {
        cb.checked = false;
        var row = document.getElementById('site-row-' + cb.value);
        if (row) row.style.opacity = checked ? '0.4' : '1';
    });
}

function onSiteCbClick() {
    var allCb = document.getElementById('all-sites-cb');
    if (allCb && allCb.checked) {
        allCb.checked = false;
        onAllSitesChange(false);
    }
}

function toggleSiteSelector(role) {
    var el = document.getElementById('site-selector');
    if (el) el.style.display = role === 'viewer' ? 'block' : 'none';
}

// ── Access modal site selector ─────────────────────────────────────────────
function onModalAllSitesChange(checked) {
    document.querySelectorAll('.modal-site-cb').forEach(function(cb) {
        cb.checked = false;
        var row = document.getElementById('modal-site-row-' + cb.value);
        if (row) row.style.opacity = checked ? '0.4' : '1';
    });
}

function onModalSiteCbClick() {
    var allCb = document.getElementById('modal-all-sites-cb');
    if (allCb && allCb.checked) {
        allCb.checked = false;
        onModalAllSitesChange(false);
    }
}

// ── Access modal open ──────────────────────────────────────────────────────
function openAccessModal(memberId, memberName, currentSiteIds) {
    document.getElementById('access-modal-name').textContent = memberName;
    document.getElementById('access-modal-form').action =
        '{{ url("account/account-users") }}/' + memberId;

    // Reset to "All sites"
    var allCb = document.getElementById('modal-all-sites-cb');
    allCb.checked = true;
    onModalAllSitesChange(true);

    if (currentSiteIds && currentSiteIds.length > 0) {
        // Uncheck "All sites", enable rows, check specific sites
        allCb.checked = false;
        onModalAllSitesChange(false);
        currentSiteIds.forEach(function(id) {
            var cb = document.querySelector('.modal-site-cb[value="' + id + '"]');
            if (cb) cb.checked = true;
        });
    }

    window.paModal.open('access-modal');
}

// Override form action to use proper route
document.getElementById('access-modal-form').addEventListener('submit', function(e) {
    e.preventDefault();
    var allCb = document.getElementById('modal-all-sites-cb');
    // If "All sites" is checked, uncheck all individual sites before submitting
    if (allCb && allCb.checked) {
        document.querySelectorAll('.modal-site-cb').forEach(function(cb) { cb.checked = false; });
    }
    this.submit();
});

// ── Delete modal ───────────────────────────────────────────────────────────
var _deleteForm = null;

function confirmDelete(form, title, body, btnLabel) {
    _deleteForm = form;
    document.getElementById('delete-modal-title').textContent = title;
    document.getElementById('delete-modal-body').innerHTML = body;
    document.getElementById('delete-modal-confirm').textContent = btnLabel;
    document.getElementById('delete-modal').style.display = 'flex';
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

// ── Init ───────────────────────────────────────────────────────────────────
(function() {
    var role = document.getElementById('role-select');
    if (role) toggleSiteSelector(role.value);
})();
</script>
@endpush
@endsection
