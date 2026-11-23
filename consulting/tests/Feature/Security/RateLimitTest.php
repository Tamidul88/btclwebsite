<?php

use App\Models\Enquiry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

beforeEach(function () {
    RateLimiter::clear('forms');
});

it('throttles the enquiry form after five submissions a minute', function () {
    // The limiter is keyed on IP; the default test IP is constant.
    for ($i = 0; $i < 5; $i++) {
        $this->post('/enquiries/contact', enquiryPayload([
            'email' => "sender{$i}@example.com",
        ]))->assertRedirect();
    }

    $this->post('/enquiries/contact', enquiryPayload(['email' => 'sixth@example.com']))
        ->assertStatus(429);

    expect(Enquiry::count())->toBe(5);
});

it('defines a login limiter that locks out after the configured attempts', function () {
    expect(config('security.login.max_attempts'))->toBe(5)
        ->and(config('security.login.decay_minutes'))->toBeGreaterThanOrEqual(15);

    $limiter = RateLimiter::limiter('login');
    expect($limiter)->not->toBeNull();

    $request = Request::create('/login', 'POST', ['email' => 'target@example.com']);
    $limits = $limiter($request);

    // Two dimensions: per-IP and per-account, so neither username rotation
    // from one host nor password spraying from many trips only one of them.
    expect($limits)->toHaveCount(2)
        ->and($limits[0]->maxAttempts)->toBe(5)
        ->and($limits[1]->maxAttempts)->toBe(5);
});

it('keys the login limiter separately per account', function () {
    $limiter = RateLimiter::limiter('login');

    $a = $limiter(Request::create('/login', 'POST', ['email' => 'a@example.com']));
    $b = $limiter(Request::create('/login', 'POST', ['email' => 'b@example.com']));

    expect($a[1]->key)->not->toBe($b[1]->key);
});

it('normalises the account key so case cannot multiply the allowance', function () {
    $limiter = RateLimiter::limiter('login');

    $lower = $limiter(Request::create('/login', 'POST', ['email' => 'target@example.com']));
    $upper = $limiter(Request::create('/login', 'POST', ['email' => 'TARGET@example.com']));

    expect($lower[1]->key)->toBe($upper[1]->key);
});

it('defines a separate limiter for the two-factor challenge', function () {
    expect(RateLimiter::limiter('two-factor'))->not->toBeNull();
});

/*
 * The honeypot answers spam with a blank 200 (BlankPageResponder), not a
 * redirect or a 4xx. That is deliberate: an error status tells a bot which
 * heuristic caught it, a blank page tells it nothing. The contract under test
 * is therefore "no record was created and no form came back", not the status.
 */

it('silently drops a submission that fills the honeypot field', function () {
    $response = $this->post('/enquiries/contact', enquiryPayload([
        'my_name' => 'i-am-a-bot',   // the honeypot field, must stay empty
    ]));

    expect(Enquiry::count())->toBe(0)
        ->and(trim($response->getContent()))->toBe('')
        ->and($response->getContent())->not->toContain('name="_token"');
});

it('silently drops a submission returned faster than a human could type it', function () {
    $response = $this->post('/enquiries/contact', enquiryPayload([
        // Timestamp in the future: the form was "rendered" after it was sent.
        'valid_from' => encrypt(now()->addMinute()->timestamp),
    ]));

    expect(Enquiry::count())->toBe(0)
        ->and(trim($response->getContent()))->toBe('');
});

it('gives a spam submission no clue about why it was rejected', function () {
    $response = $this->post('/enquiries/contact', enquiryPayload([
        'my_name' => 'bot',
    ]));

    expect($response->getContent())
        ->not->toContain('honeypot')
        ->not->toContain('spam')
        ->not->toContain('my_name');
});

it('accepts a genuine submission that leaves the honeypot empty', function () {
    $this->post('/enquiries/contact', enquiryPayload())->assertRedirect();

    expect(Enquiry::count())->toBe(1);
});
