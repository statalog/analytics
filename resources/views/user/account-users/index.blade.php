@extends('layouts.app')
@section('title', 'Account users')
@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <h4 class="mb-0" style="font-family:'Space Grotesk',sans-serif;font-weight:700">
        <i class="bi bi-people me-2" style="color:var(--pa-primary)"></i>Account users
    </h4>
</div>

<p style="color:var(--pa-text-muted);max-width:720px;margin-bottom:1.5rem">
    Give other people access to your analytics. <strong>Admins</strong> can do everything you can. <strong>Viewers</strong> see reports but can't change anything.
</p>

<div class="row g-4" style="max-width:1000px">
    <div class="col-md-5">
        <div class="pa-card">
            <h6 class="mb-3" style="font-family:'Space Grotesk',sans-serif;font-weight:700">Grant access</h6>
            <form method="POST" action="{{ route('user.account-users.store') }}">
                @csrf
                <div class="mb-3">
                    <label class="auth-label">Email</label>
                    <input type="email" name="email" class="pa-input @error('email') is-invalid @enderror" required value="{{ old('email') }}" placeholder="person@example.com">
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    <small style="color:var(--pa-text-muted);font-size:0.75rem">Must be someone already signed up on this Statalog instance.</small>
                </div>
                <div class="mb-3">
                    <label class="auth-label">Role</label>
                    <select name="role" class="pa-input" required>
                        <option value="viewer">Viewer — read-only</option>
                        <option value="admin">Admin — can edit everything</option>
                    </select>
                </div>
                <button type="submit" class="btn-pa-primary"><i class="bi bi-person-plus me-1"></i>Grant access</button>
            </form>
        </div>
    </div>

    <div class="col-md-7">
        <div class="pa-card p-0">
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
                                @if($m->isAdmin())
                                    All sites
                                @elseif($m->siteAccess->count() === 0)
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
