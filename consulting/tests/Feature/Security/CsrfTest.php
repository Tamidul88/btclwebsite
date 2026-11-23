<?php

use App\Models\Enquiry;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Tests\Support\EnforcedCsrfToken;

/*
 * These tests use EnforcedCsrfToken rather than the framework middleware.
 * VerifyCsrfToken deliberately bypasses itself under `runningUnitTests()`, so
 * asserting against it directly would prove nothing — the request would be
 * accepted with no token and the test would still pass.
 */

beforeEach(function () {
    // The web group resolves ValidateCsrfToken from the container, so binding
    // the subclass over it is what actually puts enforcement in the stack.
    // withMiddleware() alone would not help: it only un-skips a class that is
    // already there.
    $this->app->bind(ValidateCsrfToken::class, EnforcedCsrfToken::class);
});

it('rejects a form post with no CSRF token', function () {
    $this->post('/enquiries/contact', enquiryPayload())
        ->assertStatus(419);

    expect(Enquiry::count())->toBe(0);
});

it('rejects a form post with a forged CSRF token', function () {
    $this->post('/enquiries/contact', enquiryPayload([
        '_token' => str_repeat('a', 40),
    ]))->assertStatus(419);

    expect(Enquiry::count())->toBe(0);
});

it('rejects a token belonging to a different session', function () {
    $stolen = str_repeat('b', 40);

    $this->post('/enquiries/contact', enquiryPayload(['_token' => $stolen]))
        ->assertStatus(419);

    expect(Enquiry::count())->toBe(0);
});

it('accepts a form post carrying the current session token', function () {
    $this->get('/contact')->assertOk();

    $this->post('/enquiries/contact', enquiryPayload([
        '_token' => csrf_token(),
    ]))->assertRedirect();

    expect(Enquiry::count())->toBe(1);
});

it('renders a csrf field in every public form', function () {
    foreach (['/contact', '/request-consultation'] as $page) {
        $this->get($page)->assertOk()->assertSee('name="_token"', false);
    }
});
