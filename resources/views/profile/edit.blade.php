@extends('layouts.app')
@section('title', __('app.nav_profile'))
@section('content')
<div class="d-flex align-items-center gap-3 mb-4">
    <h4 class="mb-0" style="font-family:'Space Grotesk',sans-serif;font-weight:700">
        <i class="bi bi-person-circle me-2" style="color:var(--pa-primary)"></i>{{ __('app.nav_profile') }}
    </h4>
</div>

<div class="row g-4" style="max-width:720px">

    {{-- Profile information --}}
    <div class="col-12">
        <div class="pa-card">
            <h6 class="mb-1" style="font-family:'Space Grotesk',sans-serif;font-weight:700">Profile Information</h6>
            <p style="font-size:0.8125rem;color:var(--pa-text-muted);margin-bottom:1rem">Update your account's profile information and email address.</p>

            <form method="POST" action="{{ route('user.profile.update') }}">
                @csrf @method('PATCH')

                <div class="mb-3">
                    <label class="auth-label">Name</label>
                    <input type="text" name="name" class="pa-input @error('name', 'updateProfileInformation') is-invalid @enderror"
                           value="{{ old('name', $user->name) }}" required autofocus autocomplete="name">
                    @error('name', 'updateProfileInformation')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="auth-label">Email</label>
                    <input type="email" name="email" class="pa-input @error('email', 'updateProfileInformation') is-invalid @enderror"
                           value="{{ old('email', $user->email) }}" required autocomplete="username">
                    @error('email', 'updateProfileInformation')<div class="invalid-feedback">{{ $message }}</div>@enderror

                    @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                        <div class="mt-2" style="font-size:0.8125rem;color:var(--pa-text-muted)">
                            Your email address is unverified.
                            <form method="POST" action="{{ route('verification.send') }}" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-link p-0" style="font-size:0.8125rem;color:var(--pa-primary);text-decoration:underline">
                                    Click here to re-send the verification email.
                                </button>
                            </form>
                            @if (session('status') === 'verification-link-sent')
                                <div style="color:var(--pa-success);font-weight:600;margin-top:0.25rem">A new verification link has been sent.</div>
                            @endif
                        </div>
                    @endif
                </div>

                <button type="submit" class="btn-pa-primary">
                    <i class="bi bi-check-lg me-1"></i> Save
                </button>
                @if (session('status') === 'profile-updated')
                    <span style="font-size:0.8125rem;color:var(--pa-success);margin-left:0.75rem">Saved.</span>
                @endif
            </form>
        </div>
    </div>

    {{-- Update password --}}
    <div class="col-12">
        <div class="pa-card">
            <h6 class="mb-1" style="font-family:'Space Grotesk',sans-serif;font-weight:700">Update Password</h6>
            <p style="font-size:0.8125rem;color:var(--pa-text-muted);margin-bottom:1rem">Ensure your account is using a long, random password to stay secure.</p>

            <form method="POST" action="{{ route('password.update') }}">
                @csrf @method('PUT')

                <div class="mb-3">
                    <label class="auth-label">Current Password</label>
                    <input type="password" name="current_password" class="pa-input @error('current_password', 'updatePassword') is-invalid @enderror" autocomplete="current-password">
                    @error('current_password', 'updatePassword')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="auth-label">New Password</label>
                    <input type="password" name="password" class="pa-input @error('password', 'updatePassword') is-invalid @enderror" autocomplete="new-password">
                    @error('password', 'updatePassword')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="auth-label">Confirm Password</label>
                    <input type="password" name="password_confirmation" class="pa-input" autocomplete="new-password">
                </div>

                <button type="submit" class="btn-pa-primary">
                    <i class="bi bi-check-lg me-1"></i> Save
                </button>
                @if (session('status') === 'password-updated')
                    <span style="font-size:0.8125rem;color:var(--pa-success);margin-left:0.75rem">Saved.</span>
                @endif
            </form>
        </div>
    </div>

    {{-- Delete account --}}
    <div class="col-12">
        <div class="pa-card" style="border-color:var(--pa-danger-soft)">
            <h6 class="mb-1" style="font-family:'Space Grotesk',sans-serif;font-weight:700;color:var(--pa-danger)">
                <i class="bi bi-exclamation-triangle me-1"></i>Delete Account
            </h6>
            <p style="font-size:0.8125rem;color:var(--pa-text-muted);margin-bottom:1rem">
                Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting, please download any data you wish to retain.
            </p>

            <form method="POST" action="{{ route('user.profile.destroy') }}" id="delete-account-form">
                @csrf @method('DELETE')
                <input type="password" name="password" class="pa-input mb-2 @error('password', 'userDeletion') is-invalid @enderror"
                       placeholder="Enter your password to confirm" style="max-width:360px">
                @error('password', 'userDeletion')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror

                <button type="submit" class="btn-pa-danger" data-pa-confirm="delete-account">
                    <i class="bi bi-trash me-1"></i> Delete Account
                </button>
            </form>
        </div>
    </div>

</div>

<x-confirm-modal
    id="delete-account"
    variant="danger"
    icon="exclamation-triangle"
    title="Delete your account?"
    body="This is permanent. All of your sites and analytics data will be removed. This cannot be undone."
    confirmLabel="Delete Account"
/>
@endsection
