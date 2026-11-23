<?php

use App\Models\User;

it('passes on a correctly configured application', function () {
    $this->artisan('security:audit')->assertSuccessful();
});

it('fails when debug mode is on in a production check', function () {
    config()->set('app.debug', true);
    config()->set('app.env', 'production');

    $this->artisan('security:audit --production')->assertFailed();
});

it('fails when the session cookie is not httpOnly', function () {
    config()->set('session.http_only', false);

    $this->artisan('security:audit')
        ->expectsOutputToContain('Cookies are HttpOnly')
        ->assertFailed();
});

it('fails when the session is not encrypted', function () {
    config()->set('session.encrypt', false);

    $this->artisan('security:audit')->assertFailed();
});

it('fails when an executable extension is allowed for upload', function (string $extension) {
    config()->set('security.uploads.extensions', ['pdf', $extension]);

    $this->artisan('security:audit')
        ->expectsOutputToContain('No executable or scriptable extensions allowed')
        ->assertFailed();
})->with(['php', 'phtml', 'svg', 'html', 'sh']);

it('fails when two-factor enforcement is switched off', function () {
    config()->set('security.two_factor.enforce', false);

    $this->artisan('security:audit')->assertFailed();
});

it('fails when the lockout threshold is raised too high', function () {
    config()->set('security.login.max_attempts', 50);

    $this->artisan('security:audit')->assertFailed();
});

it('fails when the admin path is a well-known default', function (string $path) {
    config()->set('security.admin_path', $path);

    $this->artisan('security:audit')
        ->expectsOutputToContain('Admin path is not a default')
        ->assertFailed();
})->with(['admin', 'administrator', 'backend', 'panel', 'dashboard']);

it('fails when the attachment disk sits inside the web root', function () {
    config()->set('filesystems.disks.private.root', public_path('uploads'));

    $this->artisan('security:audit')
        ->expectsOutputToContain('Attachment disk is outside the web root')
        ->assertFailed();
});

it('treats the seeded dev account as a production failure', function () {
    User::factory()->create(['email' => 'admin@example.test', 'is_active' => true]);

    config()->set('app.env', 'production');
    config()->set('app.debug', false);

    $this->artisan('security:audit --production')
        ->expectsOutputToContain('Development seed account is absent')
        ->assertFailed();
});

it('downgrades production-only checks to warnings without the flag', function () {
    // APP_ENV is "testing" here, so the production checks cannot pass —
    // they must not fail the local run either.
    $this->artisan('security:audit')->assertSuccessful();
});
