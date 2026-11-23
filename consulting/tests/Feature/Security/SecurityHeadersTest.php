<?php

it('sends the full security header set on public pages', function () {
    $response = $this->get('/');

    $response->assertOk()
        ->assertHeader('X-Content-Type-Options', 'nosniff')
        ->assertHeader('X-Frame-Options', 'DENY')
        ->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin')
        ->assertHeader('Cross-Origin-Opener-Policy', 'same-origin');

    expect($response->headers->get('Permissions-Policy'))
        ->toContain('geolocation=()')
        ->toContain('camera=()')
        ->toContain('microphone=()');
});

it('uses a strict nonce CSP with no unsafe directives on the public site', function () {
    $response = $this->get('/');
    $csp = $response->headers->get('Content-Security-Policy');

    expect($csp)
        ->not->toContain('unsafe-inline')
        ->not->toContain('unsafe-eval')
        ->toContain("frame-ancestors 'none'")
        ->toContain("object-src 'none'")
        ->toContain("base-uri 'self'")
        ->toContain("form-action 'self'")
        ->toMatch("/script-src 'self' 'nonce-[A-Za-z0-9]{24}'/");
});

it('stamps the same nonce into the document that it advertises in the header', function () {
    $response = $this->get('/');

    preg_match("/'nonce-([A-Za-z0-9]{24})'/", $response->headers->get('Content-Security-Policy'), $header);
    preg_match('/nonce="([A-Za-z0-9]{24})"/', $response->getContent(), $body);

    expect($header[1] ?? 'a')->toBe($body[1] ?? 'b');
});

it('issues a different nonce on every request', function () {
    $first = $this->get('/')->headers->get('Content-Security-Policy');
    $second = $this->get('/')->headers->get('Content-Security-Policy');

    expect($first)->not->toBe($second);
});

it('does not advertise the PHP version', function () {
    expect($this->get('/')->headers->get('X-Powered-By'))->toBeNull();
});

it('sends HSTS only over https', function () {
    config()->set('security.hsts.enabled', true);

    expect($this->get('http://localhost/')->headers->get('Strict-Transport-Security'))->toBeNull();
});

it('relaxes CSP only for the admin panel, never for the public site', function () {
    $admin = $this->get('/'.config('security.admin_path').'/login')
        ->headers->get('Content-Security-Policy');
    $public = $this->get('/')->headers->get('Content-Security-Policy');

    expect($admin)->toContain('unsafe-eval');
    expect($public)->not->toContain('unsafe-eval');
});

it('renders custom error pages without leaking internals', function () {
    $response = $this->get('/services/does-not-exist');

    $response->assertNotFound()
        ->assertSee('Page not found')
        ->assertDontSee('Symfony')
        ->assertDontSee('vendor/laravel');
});
