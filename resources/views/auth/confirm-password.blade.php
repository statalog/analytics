<x-guest-layout>
    <div class="auth-card">
        <div class="auth-logo">
            <a href="{{ route('home') }}">{{ __('app.name') }}</a>
        </div>
        <div class="auth-heading">{{ __('auth.confirm_heading') }}</div>
        <p style="font-size:0.875rem;color:var(--pa-text-muted);margin-bottom:1.25rem">
            This is a secure area. Please confirm your password before continuing.
        </p>

        <form method="POST" action="{{ route('password.confirm') }}">
            @csrf
            <div class="mb-3">
                <label class="auth-label" for="password">{{ __('auth.field_password') }}</label>
                <input id="password" type="password" name="password" class="auth-input" required autocomplete="current-password">
                @error('password')<span class="auth-error">{{ $message }}</span>@enderror
            </div>
            <button type="submit" class="auth-btn">{{ __('auth.btn_confirm') }}</button>
        </form>
    </div>
</x-guest-layout>
