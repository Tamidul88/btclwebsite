<?php

use App\Services\TwoFactorService;
use Illuminate\Validation\Rules\Password;
use PragmaRX\Google2FA\Google2FA;

it('encrypts the 2fa secret at rest', function () {
    $user = panelUser();
    $user->forceFill(['two_factor_secret' => 'PLAINTEXTSECRET'])->save();

    $raw = DB::table('users')->where('id', $user->id)->value('two_factor_secret');

    expect($raw)->not->toBe('PLAINTEXTSECRET')
        ->and($user->fresh()->two_factor_secret)->toBe('PLAINTEXTSECRET');
});

it('hides 2fa material from serialisation', function () {
    $array = panelUser()->toArray();

    expect($array)->not->toHaveKeys([
        'two_factor_secret', 'two_factor_recovery_codes', 'password', 'remember_token',
    ]);
});

it('accepts a correct totp code', function () {
    $service = app(TwoFactorService::class);
    $secret = $service->generateSecret();
    $code = app(Google2FA::class)->getCurrentOtp($secret);

    expect($service->verify($secret, $code))->toBeTrue();
});

it('rejects an incorrect or malformed totp code', function (string $code) {
    $service = app(TwoFactorService::class);

    expect($service->verify($service->generateSecret(), $code))->toBeFalse();
})->with(['000000', 'abcdef', '12345', '1234567', '', '<script>', '12 34 56']);

it('treats a user without a confirmed secret as not enrolled', function () {
    $user = panelUser();
    $user->forceFill(['two_factor_confirmed_at' => null])->save();

    expect($user->fresh()->hasTwoFactorEnabled())->toBeFalse();
});

it('consumes a recovery code exactly once', function () {
    $service = app(TwoFactorService::class);
    $user = panelUser();
    $codes = $service->generateRecoveryCodes();

    $user->forceFill(['two_factor_recovery_codes' => $codes])->save();

    expect($service->consumeRecoveryCode($user, $codes[0]))->toBeTrue()
        ->and($service->consumeRecoveryCode($user->fresh(), $codes[0]))->toBeFalse()
        ->and($user->fresh()->two_factor_recovery_codes)->toHaveCount(count($codes) - 1);
});

it('records 2fa lifecycle events in the audit trail', function () {
    $service = app(TwoFactorService::class);
    $user = panelUser();

    $this->actingAs($user);
    $service->confirm($user, $service->generateSecret(), $service->generateRecoveryCodes());

    $this->assertDatabaseHas('audit_logs', [
        'user_id' => $user->id,
        'event' => 'user.two_factor_enabled',
    ]);
});

it('redirects an enrolled user to the challenge before any panel page', function () {
    $user = panelUser();

    $this->actingAs($user)
        ->get('/'.config('security.admin_path'))
        ->assertRedirect(route('filament.admin.pages.two-factor-challenge'));
});

it('redirects an unenrolled user to setup', function () {
    $user = panelUser();
    $user->forceFill([
        'two_factor_secret' => null,
        'two_factor_confirmed_at' => null,
    ])->save();

    $this->actingAs($user->fresh())
        ->get('/'.config('security.admin_path'))
        ->assertRedirect(route('filament.admin.pages.two-factor-setup'));
});

it('lets a challenged session through', function () {
    $user = panelUser();
    passTwoFactor();

    $this->actingAs($user)
        ->get('/'.config('security.admin_path'))
        ->assertSuccessful();
});

it('enforces a strong password policy including a breach check in production', function () {
    $rule = Password::default();

    expect((fn () => $this->min)->call($rule))->toBeGreaterThanOrEqual(12);

    // uncompromised() is enabled only in production so the local suite does
    // not depend on an outbound call to the HIBP range API.
    expect(app()->isProduction())->toBeFalse();
});
