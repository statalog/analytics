<x-guest-layout>
    <div class="auth-card">
        <div class="auth-logo">
            <a href="{{ route('home') }}">{{ __('app.name') }}</a>
        </div>
        <div class="auth-heading">{{ __('auth.reset_heading') }}</div>

        <form method="POST" action="{{ route('password.store') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $request->route('token') }}">
            <div class="mb-3">
                <label class="auth-label" for="email">{{ __('auth.field_email') }}</label>
                <input id="email" type="email" name="email" class="auth-input" value="{{ old('email', $request->email) }}" required autofocus autocomplete="username">
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
            <button type="submit" class="auth-btn">{{ __('auth.btn_reset') }}</button>
        </form>
    </div>
</x-guest-layout>
