<?php

use App\Http\Middleware\RequireTwoFactor;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

pest()->extend(TestCase::class)
    ->use(RefreshDatabase::class)
    ->in('Feature');

/**
 * Creates a panel user with roles seeded and 2FA already satisfied, so tests
 * that are about authorisation do not have to walk the 2FA flow every time.
 */
function panelUser(string $role = User::ROLE_ADMIN): User
{
    foreach ([User::ROLE_ADMIN, User::ROLE_EDITOR] as $name) {
        Role::findOrCreate($name, 'web');
    }

    $user = User::factory()->create([
        'email_verified_at' => now(),
        'is_active' => true,
        'two_factor_secret' => 'JBSWY3DPEHPK3PXP',
        'two_factor_confirmed_at' => now(),
    ]);

    $user->syncRoles([$role]);

    return $user;
}

/** Marks the current session as having passed the 2FA challenge. */
function passTwoFactor(): void
{
    session()->put(RequireTwoFactor::SESSION_KEY, now()->timestamp);
}

/** A minimal valid enquiry payload. */
function enquiryPayload(array $overrides = []): array
{
    return array_merge([
        'name' => 'Dana Whitfield',
        'email' => 'dana@example.com',
        'company' => 'Northgate Freight',
        'phone' => '020 7946 0100',
        'message' => 'We have an overnight batch process that keeps overrunning its window and would like help.',
        'consent' => '1',
        // spatie/laravel-honeypot: field must be empty, and the encrypted
        // timestamp must be older than the configured minimum.
        'my_name' => '',
        'valid_from' => encrypt(now()->subMinute()->timestamp),
    ], $overrides);
}
