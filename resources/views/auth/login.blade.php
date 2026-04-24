<x-guest-layout>
    <div class="auth-card">
        <div class="auth-logo">
            <a href="{{ route('home') }}"><img src="{{ asset('img/logo.png') }}" alt="{{ __('app.name') }}" style="height:42px;width:auto"></a>
        </div>
        <div class="auth-heading">{{ __('auth.login_heading') }}</div>
        <div class="auth-subheading">{{ __('auth.login_subheading') }}</div>

        @if(session('status'))
            <div class="pa-alert success">{{ session('status') }}</div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="mb-3">
                <label class="auth-label" for="email">{{ __('auth.field_email') }}</label>
                <input id="email" type="email" name="email" class="auth-input" value="{{ old('email') }}" required autofocus autocomplete="username">
                @error('email')<span class="auth-error">{{ $message }}</span>@enderror
            </div>
            <div class="mb-3">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <label class="auth-label mb-0" for="password">{{ __('auth.field_password') }}</label>
                    @if(Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="auth-link text-sm">{{ __('auth.link_forgot') }}</a>
                    @endif
                </div>
                <input id="password" type="password" name="password" class="auth-input" required autocomplete="current-password">
                @error('password')<span class="auth-error">{{ $message }}</span>@enderror
            </div>
            <div class="d-flex align-items-center mb-3 gap-2">
                <input type="checkbox" name="remember" id="remember" style="accent-color:var(--pa-primary)">
                <label for="remember" style="font-size:0.875rem;color:var(--pa-text-muted);cursor:pointer">{{ __('auth.field_remember') }}</label>
            </div>
            <button type="submit" class="auth-btn">{{ __('auth.btn_login') }}</button>
        </form>

        @if(Route::has('register'))
        <div class="text-center mt-3 text-sm-muted">
            <a href="{{ route('register') }}" class="auth-link">{{ __('auth.link_register') }}</a>
        </div>
        @endif
    </div>
</x-guest-layout>
