<?php
/**
 * Statalog - Open source web analytics.
 * https://statalog.com
 * @license AGPL-3.0
 */

namespace App\Services;

use App\Models\User;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Support\Str;
use PragmaRX\Google2FA\Google2FA;

class TwoFactorService
{
    public function __construct(protected Google2FA $google2fa) {}

    /** Create a new TOTP secret for a user (not yet confirmed). */
    public function generateSecret(User $user): string
    {
        $secret = $this->google2fa->generateSecretKey();

        $user->forceFill([
            'two_factor_secret'       => $secret,
            'two_factor_confirmed_at' => null,
        ])->save();

        return $secret;
    }

    /** otpauth:// URI that authenticator apps scan. */
    public function qrCodeUri(User $user): string
    {
        return $this->google2fa->getQRCodeUrl(
            config('app.name', 'Statalog'),
            $user->email,
            $user->two_factor_secret
        );
    }

    /** Render the QR code as inline SVG. */
    public function qrCodeSvg(User $user, int $size = 220): string
    {
        $writer = new Writer(new ImageRenderer(
            new RendererStyle($size, 1),
            new SvgImageBackEnd()
        ));

        return $writer->writeString($this->qrCodeUri($user));
    }

    /** Verify a 6-digit TOTP code against the user's secret. */
    public function verify(User $user, string $code): bool
    {
        if (!$user->two_factor_secret) {
            return false;
        }

        return (bool) $this->google2fa->verifyKey($user->two_factor_secret, $code);
    }

    /** Confirm 2FA for the user and generate the first set of recovery codes. */
    public function confirm(User $user): array
    {
        $codes = $this->generateRecoveryCodes();

        $user->forceFill([
            'two_factor_recovery_codes' => $codes,
            'two_factor_confirmed_at'   => now(),
        ])->save();

        return $codes;
    }

    /** Regenerate and return a fresh set of recovery codes. */
    public function regenerateRecoveryCodes(User $user): array
    {
        $codes = $this->generateRecoveryCodes();

        $user->forceFill(['two_factor_recovery_codes' => $codes])->save();

        return $codes;
    }

    /** Consume a single recovery code (returns true if valid). */
    public function useRecoveryCode(User $user, string $code): bool
    {
        $code  = trim($code);
        $codes = $user->two_factor_recovery_codes ?? [];

        $index = array_search($code, $codes, true);
        if ($index === false) {
            return false;
        }

        unset($codes[$index]);
        $user->forceFill(['two_factor_recovery_codes' => array_values($codes)])->save();

        return true;
    }

    /** Disable 2FA completely on a user. */
    public function disable(User $user): void
    {
        $user->forceFill([
            'two_factor_secret'         => null,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at'   => null,
        ])->save();
    }

    private function generateRecoveryCodes(): array
    {
        return collect(range(1, 8))
            ->map(fn () => Str::lower(Str::random(5)) . '-' . Str::lower(Str::random(5)))
            ->all();
    }
}
