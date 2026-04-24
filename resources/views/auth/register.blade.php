<x-guest-layout>
    <div class="auth-card">
        <div class="auth-logo">
            <a href="{{ route('home') }}"><img src="{{ asset('img/logo.png') }}" alt="{{ __('app.name') }}" style="height:42px;width:auto"></a>
        </div>
        <div class="auth-heading">{{ __('auth.register_heading') }}</div>
        <div class="auth-subheading">{{ __('auth.register_subheading') }}</div>

        <form method="POST" action="{{ route('register') }}">
            @csrf
            <div class="mb-3">
                <label class="auth-label" for="name">{{ __('auth.field_name') }}</label>
                <input id="name" type="text" name="name" class="auth-input" value="{{ old('name') }}" required autofocus autocomplete="name">
                @error('name')<span class="auth-error">{{ $message }}</span>@enderror
            </div>
            <div class="mb-3">
                <label class="auth-label" for="email">{{ __('auth.field_email') }}</label>
                <input id="email" type="email" name="email" class="auth-input" value="{{ old('email') }}" required autocomplete="username">
                @error('email')<span class="auth-error">{{ $message }}</span>@enderror
            </div>
            <div class="mb-3">
                <label class="auth-label" for="password">{{ __('auth.field_password') }}</label>
                <input id="password" type="password" name="password" class="auth-input" required autocomplete="new-password">
                @error('password')<span class="auth-error">{{ $message }}</span>@enderror
            </div>
            <div class="mb-3">
                <label class="auth-label" for="password_confirmation">{{ __('auth.field_password_confirm') }}</label>
                <input id="password_confirmation" type="password" name="password_confirmation" class="auth-input" required autocomplete="new-password">
                @error('password_confirmation')<span class="auth-error">{{ $message }}</span>@enderror
            </div>
            <button type="submit" class="auth-btn">{{ __('auth.btn_register') }}</button>
        </form>

        <div class="text-center mt-3 text-sm-muted">
            <a href="{{ route('login') }}" class="auth-link">{{ __('auth.link_login') }}</a>
        </div>
    </div>
</x-guest-layout>
