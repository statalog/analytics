<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('cloud::members.accept_page_title') }} — {{ config('app.name') }}</title>
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
        <div class="invite-title">{{ __('cloud::members.expired_title') }}</div>
        <div class="invite-sub">{!! __('cloud::members.expired_body', ['owner' => e($invitation->owner->name)]) !!}</div>
        <a href="{{ route('login') }}" class="btn-pa-primary w-100 text-center d-block">{{ __('cloud::members.btn_go_to_login') }}</a>

    {{-- ── LOGGED-IN USER WITH CORRECT EMAIL — CONFIRM ── --}}
    @elseif(!empty($confirm))
        <div class="invite-title">{{ __('cloud::members.accept_title') }}</div>
        <div class="invite-sub">
            {!! __('cloud::members.accept_body', ['owner' => e($invitation->owner->name), 'role' => e(ucfirst($invitation->role))]) !!}
        </div>
        @if(session('error'))
            <div class="pa-alert danger mb-3">{{ session('error') }}</div>
        @endif
        <form method="POST" action="{{ route('invitations.accept', $invitation->token) }}">
            @csrf
            <button type="submit" class="btn-pa-primary w-100">{{ __('cloud::members.btn_accept_dashboard') }}</button>
        </form>

    {{-- ── EMAIL ALREADY HAS AN ACCOUNT — SEND TO LOGIN ── --}}
    @elseif(!empty($user_exists))
        <div class="invite-title">{{ __('cloud::members.login_to_accept_title') }}</div>
        <div class="invite-sub">
            {!! __('cloud::members.login_to_accept_body', ['owner' => e($invitation->owner->name), 'email' => e($invitation->email)]) !!}
        </div>
        @if(session('error'))
            <div class="pa-alert danger mb-3">{{ session('error') }}</div>
        @endif
        <a href="{{ route('login') }}" class="btn-pa-primary w-100 text-center d-block">{{ __('cloud::members.btn_login_to_accept') }}</a>
        <p style="text-align:center;margin-top:1rem;font-size:0.875rem;color:var(--pa-text-muted)">
            {!! __('cloud::members.login_with_hint', ['email' => e($invitation->email)]) !!}
        </p>

    {{-- ── NEW USER — REGISTRATION FORM ── --}}
    @else
        <div class="invite-title">{{ __('cloud::members.create_account_title') }}</div>
        <div class="invite-sub">
            {!! __('cloud::members.create_account_body', ['owner' => e($invitation->owner->name), 'role' => e(ucfirst($invitation->role))]) !!}
        </div>

        @if(session('error'))
            <div class="pa-alert danger mb-3">{{ session('error') }}</div>
        @endif

        <form method="POST" action="{{ route('invitations.register', $invitation->token) }}">
            @csrf

            <div class="mb-3">
                <label class="auth-label">{{ __('cloud::members.label_email') }}</label>
                <input type="email" class="pa-input" value="{{ $invitation->email }}" readonly style="opacity:.7;cursor:not-allowed">
            </div>

            <div class="mb-3">
                <label class="auth-label">{{ __('cloud::members.label_your_name') }}</label>
                <input type="text" name="name" class="pa-input @error('name') is-invalid @enderror"
                       value="{{ old('name') }}" required autofocus placeholder="{{ __('cloud::members.placeholder_full_name') }}">
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label class="auth-label">{{ __('cloud::members.label_password') }}</label>
                <input type="password" name="password" class="pa-input @error('password') is-invalid @enderror"
                       required placeholder="{{ __('cloud::members.placeholder_password') }}">
                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-4">
                <label class="auth-label">{{ __('cloud::members.label_confirm_password') }}</label>
                <input type="password" name="password_confirmation" class="pa-input" required placeholder="{{ __('cloud::members.placeholder_repeat_password') }}">
            </div>

            <button type="submit" class="btn-pa-primary w-100">
                <i class="bi bi-check2-circle me-1"></i>{{ __('cloud::members.btn_create_accept') }}
            </button>
        </form>

        <p style="text-align:center;margin-top:1.25rem;font-size:0.875rem;color:var(--pa-text-muted)">
            {{ __('cloud::members.have_account') }}
            <a class="icon-primary" href="{{ route('login') }}">{{ __('cloud::members.login_instead') }}</a>
        </p>
    @endif
</div>
</body>
</html>
