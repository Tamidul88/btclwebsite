<?php

use Illuminate\Testing\TestResponse;
use Symfony\Component\HttpFoundation\Cookie;

/**
 * Note: these look the cookie up with first(fn) rather than firstWhere().
 * getCookies() returns Cookie objects, and firstWhere() reads array keys or
 * public properties — it silently returns null here, which would make every
 * assertion below vacuous.
 */
function sessionCookie(TestResponse $response): ?Cookie
{
    return collect($response->headers->getCookies())
        ->first(fn ($cookie) => $cookie->getName() === config('session.cookie'));
}

it('marks the session cookie httpOnly', function () {
    $cookie = sessionCookie($this->get('/'));

    expect($cookie)->not->toBeNull()
        ->and($cookie->isHttpOnly())->toBeTrue();
});

it('constrains the session cookie with sameSite', function () {
    $cookie = sessionCookie($this->get('/'));

    expect(strtolower((string) $cookie->getSameSite()))->toBeIn(['lax', 'strict']);
});

it('declares secure defaults in config/session.php', function () {
    /*
     * Asserted against the file source, not the resolved config: resolving it
     * would pick up the local .env overrides (http development uses
     * secure=false, sameSite=lax) and the test would describe the dev machine
     * rather than what ships. This is a drift guard on the committed defaults.
     */
    $source = file_get_contents(base_path('config/session.php'));

    expect($source)
        ->toContain("env('SESSION_SAME_SITE', 'strict')")
        ->toContain("env('SESSION_SECURE_COOKIE', true)")
        ->toContain("env('SESSION_HTTP_ONLY', true)")
        ->toContain("env('SESSION_ENCRYPT', true)")
        ->toContain("env('SESSION_LIFETIME', 30)");
});

it('ships production env defaults that keep cookies locked down', function () {
    $env = file_get_contents(base_path('.env.example'));

    expect($env)
        ->toContain('SESSION_SECURE_COOKIE=true')
        ->toContain('SESSION_HTTP_ONLY=true')
        ->toContain('SESSION_SAME_SITE=strict')
        ->toContain('SESSION_ENCRYPT=true')
        ->toContain('APP_DEBUG=false');
});

it('marks the session cookie secure when secure cookies are enabled', function () {
    config()->set('session.secure', true);

    expect(sessionCookie($this->get('/'))?->isSecure())->toBeTrue();
});

it('keeps the XSRF-TOKEN cookie readable by javascript but sameSite constrained', function () {
    // XSRF-TOKEN is httpOnly=false by design: front-end code must read it to
    // set the X-XSRF-TOKEN header. SameSite is what protects it.
    $cookie = collect($this->get('/')->headers->getCookies())
        ->first(fn ($c) => $c->getName() === 'XSRF-TOKEN');

    expect($cookie)->not->toBeNull()
        ->and($cookie->isHttpOnly())->toBeFalse()
        ->and(strtolower((string) $cookie->getSameSite()))->toBeIn(['lax', 'strict']);
});
