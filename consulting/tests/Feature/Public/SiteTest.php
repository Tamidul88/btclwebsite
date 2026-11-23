<?php

it('renders every public page', function (string $path) {
    $this->get($path)->assertOk();
})->with([
    '/', '/about', '/services', '/case-studies',
    '/contact', '/request-consultation',
    '/legal/privacy', '/legal/terms', '/legal/cookies',
]);

it('renders every configured service page', function () {
    foreach (array_keys(config('site.services')) as $slug) {
        $this->get("/services/{$slug}")
            ->assertOk()
            ->assertSee(config("site.services.{$slug}.name"));
    }
});

it('renders every configured case study', function () {
    foreach (array_keys(config('site.case_studies')) as $slug) {
        $this->get("/case-studies/{$slug}")
            ->assertOk()
            ->assertSee(config("site.case_studies.{$slug}.name"));
    }
});

it('404s on unknown slugs rather than leaking a view name', function (string $path) {
    $this->get($path)->assertNotFound();
})->with([
    '/services/nope',
    '/case-studies/nope',
    '/legal/nope',
    '/legal/..%2f..%2fetc%2fpasswd',
]);

it('serves a valid sitemap listing every public url', function () {
    $response = $this->get('/sitemap.xml')
        ->assertOk()
        ->assertHeader('Content-Type', 'application/xml');

    $xml = simplexml_load_string($response->getContent());

    expect($xml)->not->toBeFalse();

    /*
     * xpath with local-name() rather than collect($xml->url): wrapping a
     * SimpleXMLElement in collect() does not iterate its siblings — it yields
     * only the last node, which silently reduced this assertion to checking a
     * single URL. local-name() also sidesteps the sitemap's XML namespace.
     */
    $locations = array_map(
        fn ($node) => (string) $node,
        $xml->xpath('//*[local-name()="loc"]') ?: []
    );

    // Vacuity guard: the sitemap must actually list the whole site.
    expect($locations)->toHaveCount(
        6 + count(config('site.services')) + count(config('site.case_studies')) + 3
    );

    foreach (array_keys(config('site.services')) as $slug) {
        expect($locations)->toContain(route('services.show', $slug));
    }

    expect($locations)->toContain(route('home'));
});

it('keeps the admin path out of the sitemap', function () {
    expect($this->get('/sitemap.xml')->getContent())
        ->not->toContain(config('security.admin_path'));
});

it('disallows the admin path in robots.txt', function () {
    $this->get('/robots.txt')
        ->assertOk()
        ->assertSee('Disallow: /'.config('security.admin_path'))
        ->assertSee('Sitemap: '.route('sitemap'));
});

it('emits the seo meta tags each page needs', function () {
    $html = $this->get('/services/platform-modernisation')->getContent();

    expect($html)
        ->toContain('<link rel="canonical"')
        ->toContain('property="og:title"')
        ->toContain('property="og:description"')
        ->toContain('property="og:url"')
        ->toContain('name="twitter:card"')
        ->toContain('application/ld+json');
});

it('publishes valid Organization schema', function () {
    $html = $this->get('/')->getContent();

    preg_match('#<script type="application/ld\+json"[^>]*>(.*?)</script>#s', $html, $m);
    $data = json_decode(trim($m[1] ?? ''), true);

    expect($data)->toBeArray()
        ->and($data['@type'])->toBe('Organization')
        ->and($data['name'])->toBe(config('site.company'))
        ->and($data['address']['@type'])->toBe('PostalAddress');
});

it('marks legal pages noindex', function () {
    expect($this->get('/legal/privacy')->getContent())
        ->toContain('name="robots" content="noindex, follow"');
});

it('lazy loads images that are not above the fold', function () {
    // No <img> is emitted on these placeholder pages, but any that appear
    // later must carry loading="lazy" unless explicitly marked eager.
    $html = $this->get('/')->getContent();

    preg_match_all('/<img\b(?![^>]*loading=)[^>]*>/i', $html, $missing);

    expect($missing[0])->toBeEmpty();
});
