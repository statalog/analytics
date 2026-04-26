<x-guest-layout>
    <div class="auth-card">
        <div class="auth-logo">
            <a href="{{ route('home') }}"><img src="{{ asset('img/logo.png') }}" alt="{{ __('app.name') }}" style="height:42px;width:auto"></a>
        </div>

        <div class="auth-heading">{{ __('auth.tfa_heading') }}</div>
        <div class="auth-subheading" id="auth-subheading">{{ __('auth.tfa_subheading_code') }}</div>

        @if ($errors->any())
            <div class="pa-alert danger">{{ $errors->first() }}</div>
        @endif

        {{-- Authenticator code tab (default) --}}
        <form method="POST" action="{{ route('two-factor.challenge') }}" id="tfa-code-form">
            @csrf
            <div class="mb-3">
                <label class="auth-label">{{ __('auth.tfa_label_code') }}</label>
                <input type="text" name="code" inputmode="numeric" autocomplete="one-time-code"
                       pattern="[0-9]*" maxlength="6" required autofocus
                       class="pa-input" placeholder="123456"
                       style="letter-spacing:0.3em;text-align:center;font-family:monospace;font-size:1.125rem">
            </div>

            <button type="submit" class="btn-pa-primary" style="width:100%">{{ __('auth.btn_verify') }}</button>

            <div class="mt-3 text-center">
                <a href="#" onclick="showRecovery(event)" class="auth-link text-sm">{{ __('auth.tfa_use_recovery') }}</a>
            </div>
        </form>

        {{-- Recovery code tab (hidden by default) --}}
        <form method="POST" action="{{ route('two-factor.challenge') }}" id="tfa-recovery-form" style="display:none">
            @csrf
            <div class="mb-3">
                <label class="auth-label">{{ __('auth.tfa_label_recovery') }}</label>
                <input type="text" name="recovery_code" autocomplete="one-time-code"
                       class="pa-input" placeholder="xxxxx-xxxxx"
                       style="font-family:monospace;letter-spacing:0.1em">
            </div>

            <button type="submit" class="btn-pa-primary" style="width:100%">{{ __('auth.btn_verify') }}</button>

            <div class="mt-3 text-center">
                <a href="#" onclick="showCode(event)" class="auth-link text-sm">{{ __('auth.tfa_use_code') }}</a>
            </div>
        </form>
    </div>

    <script>
    function showRecovery(e) {
        e.preventDefault();
        document.getElementById('tfa-code-form').style.display = 'none';
        var f = document.getElementById('tfa-recovery-form');
        f.style.display = '';
        f.querySelector('[name=recovery_code]').focus();
        document.getElementById('auth-subheading').textContent = @json(__('auth.tfa_subheading_recovery'));
    }
    function showCode(e) {
        e.preventDefault();
        document.getElementById('tfa-recovery-form').style.display = 'none';
        var f = document.getElementById('tfa-code-form');
        f.style.display = '';
        f.querySelector('[name=code]').focus();
        document.getElementById('auth-subheading').textContent = @json(__('auth.tfa_subheading_code'));
    }
    </script>
</x-guest-layout>
