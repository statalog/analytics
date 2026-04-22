@extends('layouts.app')
@section('title', __('app.nav_profile'))
@section('content')
<div class="d-flex align-items-center gap-3 mb-4">
    <h4 class="mb-0" style="font-family:'Space Grotesk',sans-serif;font-weight:700">
        <i class="bi bi-person-circle me-2" style="color:var(--pa-primary)"></i>{{ __('app.nav_profile') }}
    </h4>
</div>

<div class="row g-4">

    {{-- Profile information --}}
    <div class="col-lg-6">
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

    {{-- Right column: password + 2FA --}}
    <div class="col-lg-6">
        <div class="pa-card mb-4">
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

        @include('profile.partials.two-factor')
    </div>

</div>
@endsection
