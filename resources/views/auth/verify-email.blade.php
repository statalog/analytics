<x-guest-layout>
    <div class="auth-card">
        <div class="auth-logo">
            <a href="{{ route('home') }}"><img src="{{ asset('img/logo.png') }}" alt="{{ __('app.name') }}" style="height:42px;width:auto"></a>
        </div>
        <div class="auth-heading">{{ __('auth.verify_heading') }}</div>

        <p style="font-size:0.875rem;color:var(--pa-text-muted);margin-bottom:1.25rem">
            {{ __('auth.verify_body') }}
        </p>

        @if(session('status') == 'verification-link-sent')
            <div class="pa-alert success">A new verification link has been sent to your email address.</div>
        @endif

        <form method="POST" action="{{ route('verification.send') }}" class="mb-3">
            @csrf
            <button type="submit" class="auth-btn">{{ __('auth.verify_resend') }}</button>
        </form>

        <form method="POST" action="{{ route('logout') }}" class="text-center">
            @csrf
            <button type="submit" class="auth-link" style="background:none;border:none;cursor:pointer">{{ __('auth.btn_logout') }}</button>
        </form>
    </div>
</x-guest-layout>
