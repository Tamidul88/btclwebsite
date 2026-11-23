<?php

namespace App\Services;

use App\Models\User;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Support\Str;
use PragmaRX\Google2FA\Google2FA;

/**
 * TOTP enrolment and verification.
 *
 * The secret is generated server-side, stored encrypted (see the User cast),
 * and only becomes active once the user has proved they can produce a code
 * from it — so a half-finished enrolment can never lock anyone out.
 */
class TwoFactorService
{
    public function __construct(
        private readonly Google2FA $google2fa,
        private readonly AuditLogger $audit,
    ) {}

    public function generateSecret(): string
    {
        return $this->google2fa->generateSecretKey(32);
    }

    /** otpauth:// URI for the authenticator app. */
    public function provisioningUri(User $user, string $secret): string
    {
        return $this->google2fa->getQRCodeUrl(
            config('site.company'),
            $user->email,
            $secret,
        );
    }

    /** Inline SVG QR code. Rendered as a data URI so no image is written to disk. */
    public function qrCodeSvg(string $uri): string
    {
        $writer = new Writer(new ImageRenderer(
            new RendererStyle(200, 0),
            new SvgImageBackEnd,
        ));

        return $writer->writeString($uri);
    }

    public function verify(string $secret, string $code): bool
    {
        // Codes are digits only; reject anything else before it reaches the
        // library, and compare in constant time inside it.
        if (preg_match('/^\d{6}$/', $code) !== 1) {
            return false;
        }

        return (bool) $this->google2fa->verifyKey(
            $secret,
            $code,
            config('security.two_factor.window'),
        );
    }

    /** @return list<string> */
    public function generateRecoveryCodes(): array
    {
        $count = (int) config('security.two_factor.recovery_code_count');

        return array_map(
            fn () => Str::upper(Str::random(5).'-'.Str::random(5)),
            range(1, $count),
        );
    }

    /** @param  list<string>  $recoveryCodes */
    public function confirm(User $user, string $secret, array $recoveryCodes): void
    {
        $user->forceFill([
            'two_factor_secret' => $secret,
            'two_factor_recovery_codes' => $recoveryCodes,
            'two_factor_confirmed_at' => now(),
        ])->save();

        $this->audit->log('user.two_factor_enabled', $user);
    }

    /**
     * Consumes a recovery code. Each is single-use: it is removed from the
     * stored set on success, so a leaked backup sheet cannot be replayed.
     */
    public function consumeRecoveryCode(User $user, string $code): bool
    {
        $codes = $user->two_factor_recovery_codes ?? [];
        $normalised = Str::upper(trim($code));

        foreach ($codes as $index => $stored) {
            if (hash_equals($stored, $normalised)) {
                unset($codes[$index]);

                $user->forceFill([
                    'two_factor_recovery_codes' => array_values($codes),
                ])->save();

                $this->audit->log('user.recovery_code_used', $user, [
                    'remaining' => count($codes),
                ]);

                return true;
            }
        }

        return false;
    }

    public function disable(User $user): void
    {
        $user->forceFill([
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at' => null,
        ])->save();

        $this->audit->log('user.two_factor_disabled', $user);
    }
}
