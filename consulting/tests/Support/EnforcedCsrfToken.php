<?php

namespace Tests\Support;

use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;

/**
 * Laravel's ValidateCsrfToken short-circuits whenever the app is running unit
 * tests:
 *
 *     if ($this->isReading($request) || $this->runningUnitTests() || ...)
 *
 * Convenient for ordinary feature tests, but it means a CSRF test written
 * against the real middleware asserts nothing — the request sails through
 * with no token and the test still passes. This subclass disables the bypass
 * and is bound over the framework class for the duration of a CSRF test.
 */
class EnforcedCsrfToken extends ValidateCsrfToken
{
    protected function runningUnitTests(): bool
    {
        return false;
    }
}
