<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accept Invitation — {{ config('app.name') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('img/favicon.png') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('css/statalog.css') }}">
    <style>
    body { background: var(--pa-bg); display: flex; align-items: center; justify-content: center; min-height: 100vh; font-family: 'Inter', sans-serif; }
    .invite-card { background: var(--pa-card-bg); border: 1px solid var(--pa-border); border-radius: 1rem; padding: 2.5rem 2rem; width: 100%; max-width: 440px; }
    .invite-logo { display: block; margin: 0 auto 1.75rem; height: 32px; }
    .invite-title { font-family: 'Space Grotesk', sans-serif; font-size: 1.5rem; font-weight: 700; text-align: center; margin-bottom: 0.5rem; }
    .invite-sub { color: var(--pa-text-muted); font-size: 0.9375rem; text-align: center; margin-bottom: 1.75rem; line-height: 1.5; }
    </style>
</head>
<body>
<div class="invite-card">
    <a href="{{ route('home') }}">
        <img src="{{ asset('img/logo.png') }}" alt="{{ config('app.name') }}" class="invite-logo">
    </a>

    {{-- ── EXPIRED ── --}}
    @if(!empty($expired))
        <div class="invite-title">Invitation expired</div>
        <div class="invite-sub">This invitation link is no longer valid. Ask <strong>{{ $invitation->owner->name }}</strong> to send a new invitation.</div>
        <a href="{{ route('login') }}" class="btn-pa-primary w-100 text-center d-block">Go to login</a>

    {{-- ── LOGGED-IN USER WITH CORRECT EMAIL — CONFIRM ── --}}
    @elseif(!empty($confirm))
        <div class="invite-title">Accept invitation</div>
        <div class="invite-sub">
            You've been invited by <strong>{{ $invitation->owner->name }}</strong> as
            <strong>{{ ucfirst($invitation->role) }}</strong>.
        </div>
        @if(session('error'))
            <div class="pa-alert danger mb-3">{{ session('error') }}</div>
        @endif
        <form method="POST" action="{{ route('invitations.accept', $invitation->token) }}">
            @csrf
            <button type="submit" class="btn-pa-primary w-100">Accept &amp; go to dashboard</button>
        </form>

    {{-- ── EMAIL ALREADY HAS AN ACCOUNT — SEND TO LOGIN ── --}}
    @elseif(!empty($user_exists))
        <div class="invite-title">Log in to accept</div>
        <div class="invite-sub">
            <strong>{{ $invitation->owner->name }}</strong> invited <strong>{{ $invitation->email }}</strong>.
            You already have an account — log in to accept the invitation.
        </div>
        @if(session('error'))
            <div class="pa-alert danger mb-3">{{ session('error') }}</div>
        @endif
        <a href="{{ route('login') }}" class="btn-pa-primary w-100 text-center d-block">Log in to accept</a>
        <p style="text-align:center;margin-top:1rem;font-size:0.875rem;color:var(--pa-text-muted)">
            Make sure you log in with <strong>{{ $invitation->email }}</strong>.
        </p>

    {{-- ── NEW USER — REGISTRATION FORM ── --}}
    @else
        <div class="invite-title">Create your account</div>
        <div class="invite-sub">
            <strong>{{ $invitation->owner->name }}</strong> invited you as <strong>{{ ucfirst($invitation->role) }}</strong>.
            Set a password to create your account and accept.
        </div>

        @if(session('error'))
            <div class="pa-alert danger mb-3">{{ session('error') }}</div>
        @endif

        <form method="POST" action="{{ route('invitations.register', $invitation->token) }}">
            @csrf

            <div class="mb-3">
                <label class="auth-label">Email address</label>
                <input type="email" class="pa-input" value="{{ $invitation->email }}" readonly style="opacity:.7;cursor:not-allowed">
            </div>

            <div class="mb-3">
                <label class="auth-label">Your name</label>
                <input type="text" name="name" class="pa-input @error('name') is-invalid @enderror"
                       value="{{ old('name') }}" required autofocus placeholder="Full name">
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label class="auth-label">Password</label>
                <input type="password" name="password" class="pa-input @error('password') is-invalid @enderror"
                       required placeholder="Choose a strong password">
                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-4">
                <label class="auth-label">Confirm password</label>
                <input type="password" name="password_confirmation" class="pa-input" required placeholder="Repeat password">
            </div>

            <button type="submit" class="btn-pa-primary w-100">
                <i class="bi bi-check2-circle me-1"></i>Create account &amp; accept
            </button>
        </form>

        <p style="text-align:center;margin-top:1.25rem;font-size:0.875rem;color:var(--pa-text-muted)">
            Already have an account?
            <a href="{{ route('login') }}" style="color:var(--pa-primary)">Log in instead</a>
        </p>
    @endif
</div>
</body>
</html>
