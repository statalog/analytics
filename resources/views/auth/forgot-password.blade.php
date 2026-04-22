<x-guest-layout>
    <div class="auth-card">
        <div class="auth-logo">
            <a href="{{ route('home') }}">{{ __('app.name') }}</a>
        </div>
        <div class="auth-heading">{{ __('auth.forgot_heading') }}</div>
        <div class="auth-subheading">{{ __('auth.forgot_subheading') }}</div>

        @if(session('status'))
            <div class="pa-alert success">{{ session('status') }}</div>
        @endif

        <form method="POST" action="{{ route('password.email') }}">
            @csrf
            <div class="mb-3">
                <label class="auth-label" for="email">{{ __('auth.field_email') }}</label>
                <input id="email" type="email" name="email" class="auth-input" value="{{ old('email') }}" required autofocus>
                @error('email')<span class="auth-error">{{ $message }}</span>@enderror
            </div>
            <button type="submit" class="auth-btn">{{ __('auth.btn_send_link') }}</button>
        </form>

        <div class="text-center mt-3">
            <a href="{{ route('login') }}" class="auth-link">{{ __('auth.link_login') }}</a>
        </div>
    </div>
</x-guest-layout>
