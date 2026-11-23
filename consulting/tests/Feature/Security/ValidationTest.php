<?php

use App\Models\Enquiry;

it('requires the fields the form marks as required', function () {
    $this->post('/enquiries/contact', [
        'my_name' => '',
        'valid_from' => encrypt(now()->subMinute()->timestamp),
    ])->assertSessionHasErrors(['name', 'email', 'message', 'consent']);

    expect(Enquiry::count())->toBe(0);
});

it('requires explicit consent before storing personal data', function () {
    $payload = enquiryPayload();
    unset($payload['consent']);

    $this->post('/enquiries/contact', $payload)->assertSessionHasErrors('consent');

    expect(Enquiry::count())->toBe(0);
});

it('caps every field so unbounded input cannot be submitted', function () {
    $this->post('/enquiries/contact', enquiryPayload([
        'name' => str_repeat('a', 500),
        'message' => str_repeat('b', 10000),
    ]))->assertSessionHasErrors(['name', 'message']);
});

it('rejects a service value outside the configured allowlist', function () {
    $this->post('/enquiries/consultation', enquiryPayload([
        'service' => 'definitely-not-a-real-service',
    ]))->assertSessionHasErrors('service');
});

it('rejects a budget value outside the configured allowlist', function () {
    $this->post('/enquiries/consultation', enquiryPayload([
        'budget' => '"><script>',
    ]))->assertSessionHasErrors('budget');
});

it('rejects a phone number containing letters or markup', function () {
    $this->post('/enquiries/contact', enquiryPayload([
        'phone' => '<script>alert(1)</script>',
    ]))->assertSessionHasErrors('phone');
});

it('rejects an unroutable email address', function (string $email) {
    $this->post('/enquiries/contact', enquiryPayload(['email' => $email]))
        ->assertSessionHasErrors('email');
})->with([
    'no at sign' => 'not-an-email',
    'double at' => 'a@@example.com',
    'trailing dot' => 'user@example.',
    'space inside' => 'user name@example.com',
    'html' => '<a@b.com>',
]);

it('never mass-assigns a server-controlled column from request input', function () {
    $this->post('/enquiries/contact', enquiryPayload([
        'type' => 'consultation',          // route decides this, not the body
        'handled_at' => now()->toString(),
        'handled_by' => 999,
        'ip_hash' => 'attacker-supplied',
        'attachment_path' => '../../.env',
        'id' => 4242,
    ]))->assertRedirect();

    $enquiry = Enquiry::sole();

    expect($enquiry->type)->toBe(Enquiry::TYPE_CONTACT)   // from the URL
        ->and($enquiry->handled_at)->toBeNull()
        ->and($enquiry->handled_by)->toBeNull()
        ->and($enquiry->attachment_path)->toBeNull()
        ->and($enquiry->id)->not->toBe(4242)
        ->and($enquiry->ip_hash)->not->toBe('attacker-supplied');
});

it('hashes the submitter ip rather than storing it in the clear', function () {
    $this->post('/enquiries/contact', enquiryPayload())->assertRedirect();

    $enquiry = Enquiry::sole();

    expect($enquiry->ip_hash)
        ->toMatch('/^[a-f0-9]{64}$/')
        ->not->toContain('127.0.0.1');
});

it('hides sensitive columns from serialisation', function () {
    $this->post('/enquiries/contact', enquiryPayload())->assertRedirect();

    $array = Enquiry::sole()->toArray();

    expect($array)->not->toHaveKeys(['ip_hash', 'user_agent', 'attachment_path']);
});

it('rejects an unknown enquiry type in the url', function () {
    $this->post('/enquiries/nonsense', enquiryPayload())->assertNotFound();
});
