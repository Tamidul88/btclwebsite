<?php

use App\Models\Enquiry;

/*
 * The forms echo old() input back into the page on a validation failure, which
 * is where reflected XSS would live if any output escaping were missed.
 *
 * Note on assertion style: checking for a substring like "onerror=alert(1)" is
 * meaningless, because that text survives correct escaping intact — only the
 * surrounding < and " are encoded. The meaningful assertion is that no raw tag
 * is emitted and that the angle brackets came back as entities.
 */

$payloads = [
    'script tag' => '<script>alert(1)</script>',
    'img onerror' => '"><img src=x onerror=alert(1)>',
    'svg onload' => '<svg/onload=alert(1)>',
    'attribute break' => '" autofocus onfocus="alert(1)',
    'nested tag' => '<scr<script>ipt>alert(1)</scr</script>ipt>',
    'body onload' => '</textarea><body onload=alert(1)>',
];

it('never reflects a raw tag back into the document', function (string $payload) {
    // ->from() matters: without it the failed POST redirects to "/", a page
    // with no form and no old input, and every assertion below would pass
    // vacuously. We must land back on the page that echoes the payload.
    $html = $this->from('/contact')
        ->followingRedirects()
        ->post('/enquiries/contact', enquiryPayload([
            'name' => $payload,
            'message' => 'too short',   // forces the form to redisplay
        ]))
        ->getContent();

    // Guard against the vacuous case: prove we are on the form page.
    expect($html)->toContain('name="message"');

    // Strip the legitimate markup of the page, leaving only what was echoed.
    // Any of these appearing means the payload was emitted as live markup.
    expect($html)
        ->not->toContain('<script>alert(1)</script>')
        ->not->toContain('<img src=x')
        ->not->toContain('<svg/onload')
        ->not->toContain('<body onload')
        ->not->toContain('</textarea><body');
})->with($payloads);

it('re-encodes the angle brackets of a reflected payload', function () {
    $html = $this->from('/contact')
        ->followingRedirects()
        ->post('/enquiries/contact', enquiryPayload([
            'name' => '<script>alert(1)</script>',
            'message' => 'too short',
        ]))
        ->getContent();

    expect($html)
        ->toContain('name="message"')      // we are on the form page
        ->toContain('&lt;script&gt;')      // payload came back escaped
        ->not->toContain('<script>alert(1)</script>');
});

it('stores payloads verbatim but never renders them as markup', function () {
    $payload = '<script>alert("stored")</script>';

    $this->post('/enquiries/contact', enquiryPayload([
        'message' => $payload.' — our batch job overruns every night and we need help with it.',
    ]))->assertRedirect();

    $enquiry = Enquiry::sole();

    // Stored as data, unmodified. Sanitising on write destroys information and
    // is the wrong layer; escaping belongs on output.
    expect($enquiry->message)->toContain($payload);

    $user = panelUser();
    passTwoFactor();

    $html = $this->actingAs($user)
        ->get('/'.config('security.admin_path').'/enquiries/'.$enquiry->id)
        ->assertOk()
        ->getContent();

    /*
     * The security property is that no executable tag reaches the document.
     * Filament renders through Livewire, which serialises the record into a
     * JSON snapshot and escapes "<" as < rather than &lt; — a different
     * encoding from plain Blade, equally safe, because the HTML parser never
     * sees a tag boundary either way.
     */
    expect($html)
        ->not->toContain('<script>alert("stored")</script>')
        ->not->toContain('<script>alert(')
        ->and(
            str_contains($html, '&lt;script&gt;')
            || str_contains(strtolower($html), '<script>')
        )->toBeTrue('payload should be present only in an escaped form');
});

it('strips CR and LF from single-line fields so mail headers cannot be forged', function () {
    $this->post('/enquiries/contact', enquiryPayload([
        'name' => "Dana\r\nBcc: attacker@evil.test",
        'company' => "Acme\nX-Injected: yes",
    ]))->assertRedirect();

    $enquiry = Enquiry::sole();

    expect($enquiry->name)->not->toContain("\r")->not->toContain("\n")
        ->and($enquiry->company)->not->toContain("\n")
        // The text survives, flattened and inert — we are not silently
        // discarding the user's words, just the control characters.
        ->and($enquiry->name)->toContain('Bcc: attacker@evil.test');
});

it('preserves real newlines inside the message body', function () {
    $this->post('/enquiries/contact', enquiryPayload([
        'message' => "First paragraph about the batch job.\n\nSecond paragraph with more detail.",
    ]))->assertRedirect();

    expect(Enquiry::sole()->message)->toContain("\n\n");
});

it('normalises CRLF to LF in the message body', function () {
    $this->post('/enquiries/contact', enquiryPayload([
        'message' => "Line one about our overnight batch.\r\nLine two with further detail.",
    ]))->assertRedirect();

    expect(Enquiry::sole()->message)->not->toContain("\r")->toContain("\n");
});

it('rejects an email address carrying a CRLF payload', function () {
    $this->post('/enquiries/contact', enquiryPayload([
        'email' => "victim@example.com\r\nBcc: attacker@evil.test",
    ]))->assertSessionHasErrors('email');

    expect(Enquiry::count())->toBe(0);
});

it('rejects the display-name email form that carries a header break', function () {
    $this->post('/enquiries/contact', enquiryPayload([
        'email' => '"dana@example.com" <attacker@evil.test>',
    ]))->assertSessionHasErrors('email');

    expect(Enquiry::count())->toBe(0);
});
