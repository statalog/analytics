@php
    $user    = $user ?? auth()->user();
    $enabled = $user->hasTwoFactorEnabled();
    $mode    = session('tfa_mode'); // 'setup' | 'codes' | null
    $codes   = session('tfa_codes', []);
    $inSetup = !$enabled && $user->two_factor_secret && $mode === 'setup';
@endphp

<div class="pa-card">
    <div class="d-flex align-items-center justify-content-between mb-2">
        <h6 class="mb-0" style="font-family:'Space Grotesk',sans-serif;font-weight:700">
            <i class="bi bi-shield-lock me-1" style="color:var(--pa-primary)"></i>Two-Factor Authentication
        </h6>
        @if($enabled)
            <span style="background:color-mix(in srgb, var(--pa-success) 15%, transparent);color:var(--pa-success);border-radius:9999px;padding:0.2rem 0.75rem;font-size:0.75rem;font-weight:600">
                <i class="bi bi-check-circle-fill me-1"></i>Enabled
            </span>
        @else
            <span style="background:var(--pa-input-bg);color:var(--pa-text-muted);border-radius:9999px;padding:0.2rem 0.75rem;font-size:0.75rem;font-weight:600">Disabled</span>
        @endif
    </div>

    <p style="font-size:0.8125rem;color:var(--pa-text-muted);margin-bottom:1rem">
        Add an extra layer of security by requiring a code from your authenticator app in addition to your password.
    </p>

    {{-- State: setup — QR + verify code --}}
    @if($inSetup)
        @php
            $service = app(\App\Services\TwoFactorService::class);
            $qrSvg   = $service->qrCodeSvg($user, 200);
            $secret  = $user->two_factor_secret;
        @endphp
        <div style="border-top:1px solid var(--pa-border);padding-top:1rem;margin-top:0.5rem">
            <div class="row g-3 align-items-start">
                <div class="col-md-auto">
                    <div style="background:#fff;padding:0.625rem;border-radius:var(--pa-radius);display:inline-block">{!! $qrSvg !!}</div>
                </div>
                <div class="col-md">
                    <ol style="font-size:0.875rem;color:var(--pa-text);padding-left:1.125rem;margin-bottom:1rem">
                        <li class="mb-1">Install an authenticator app (Google Authenticator, Authy, 1Password, Bitwarden).</li>
                        <li class="mb-1">Scan the QR code, or enter this key manually:
                            <code style="display:inline-block;background:var(--pa-input-bg);padding:0.125rem 0.375rem;border-radius:4px;font-size:0.8125rem;margin-top:0.25rem;word-break:break-all">{{ $secret }}</code>
                        </li>
                        <li>Enter the 6-digit code your app generates below to confirm.</li>
                    </ol>

                    <form method="POST" action="{{ route('user.two-factor.confirm') }}" class="d-flex gap-2 flex-wrap align-items-start">
                        @csrf
                        <div>
                            <input type="text" name="code" inputmode="numeric" autocomplete="one-time-code"
                                   pattern="[0-9]*" maxlength="6" required autofocus
                                   class="pa-input @error('code') is-invalid @enderror"
                                   placeholder="123456" style="width:140px;letter-spacing:0.2em;text-align:center;font-family:monospace;font-size:1rem">
                            @error('code')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                        <button type="submit" class="btn-pa-primary"><i class="bi bi-check-lg me-1"></i>Confirm</button>
                        <form method="POST" action="{{ route('user.two-factor.cancel') }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn-pa-outline">Cancel</button>
                        </form>
                    </form>
                </div>
            </div>
        </div>

    {{-- State: codes — show recovery codes (just generated or requested) --}}
    @elseif($mode === 'codes' && !empty($codes))
        <div style="border-top:1px solid var(--pa-border);padding-top:1rem;margin-top:0.5rem">
            <div class="alert" style="background:color-mix(in srgb, var(--pa-warning) 10%, transparent);color:var(--pa-text);border:1px solid color-mix(in srgb, var(--pa-warning) 40%, transparent);border-radius:var(--pa-radius);padding:0.75rem 1rem;font-size:0.8125rem">
                <i class="bi bi-exclamation-triangle me-1" style="color:var(--pa-warning)"></i>
                Store these recovery codes somewhere safe. Each code can be used <strong>once</strong> if you lose access to your authenticator. They will not be shown again.
            </div>
            <div id="recovery-codes-box" style="background:var(--pa-input-bg);border-radius:var(--pa-radius);padding:1rem;font-family:monospace;font-size:0.9rem;display:grid;grid-template-columns:repeat(2,1fr);gap:0.375rem 1.5rem">
                @foreach($codes as $c)
                    <div>{{ $c }}</div>
                @endforeach
            </div>
            <div class="d-flex gap-2 mt-3">
                <button type="button" class="btn-pa-outline" onclick="copyRecoveryCodes(this)">
                    <i class="bi bi-clipboard me-1"></i>Copy
                </button>
                <a href="{{ route('user.profile.edit') }}" class="btn-pa-outline">Done</a>
            </div>
        </div>
        <script>
        function copyRecoveryCodes(btn) {
            var text = Array.from(document.querySelectorAll('#recovery-codes-box > div')).map(function(d){return d.textContent.trim();}).join('\n');
            navigator.clipboard.writeText(text).then(function() {
                var original = btn.innerHTML;
                btn.innerHTML = '<i class="bi bi-check-lg me-1"></i>Copied';
                setTimeout(function(){ btn.innerHTML = original; }, 2000);
            });
        }
        </script>

    {{-- State: enabled — management actions --}}
    @elseif($enabled)
        <div class="d-flex flex-wrap gap-2" style="border-top:1px solid var(--pa-border);padding-top:1rem;margin-top:0.5rem">
            <form method="POST" action="{{ route('user.two-factor.recovery-codes') }}" class="d-inline">
                @csrf
                <button type="submit" class="btn-pa-outline">
                    <i class="bi bi-key me-1"></i>Show recovery codes
                </button>
            </form>
            <form method="POST" action="{{ route('user.two-factor.regenerate') }}" class="d-inline">
                @csrf
                <button type="submit" class="btn-pa-outline" data-pa-confirm="regenerate-codes">
                    <i class="bi bi-arrow-clockwise me-1"></i>Regenerate codes
                </button>
            </form>
            <form method="POST" action="{{ route('user.two-factor.disable') }}" class="d-inline ms-auto">
                @csrf @method('DELETE')
                <button type="submit" class="btn-pa-danger-outline" data-pa-confirm="disable-2fa">
                    <i class="bi bi-shield-x me-1"></i>Disable 2FA
                </button>
            </form>
        </div>

    {{-- State: disabled — enable button --}}
    @else
        <form method="POST" action="{{ route('user.two-factor.start') }}">
            @csrf
            <button type="submit" class="btn-pa-primary">
                <i class="bi bi-shield-plus me-1"></i>Enable two-factor authentication
            </button>
        </form>
    @endif
</div>

<x-confirm-modal
    id="regenerate-codes"
    variant="warning"
    icon="arrow-clockwise"
    title="Regenerate recovery codes?"
    body="Your current recovery codes will immediately stop working. Make sure to save the new set."
    confirmLabel="Regenerate"
/>

<x-confirm-modal
    id="disable-2fa"
    variant="danger"
    icon="shield-x"
    title="Disable two-factor authentication?"
    body="Your account will only be protected by its password. You can re-enable 2FA at any time."
    confirmLabel="Disable"
/>
