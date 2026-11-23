<?php

use App\Models\AuditLog;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/*
 * These assert on the security channel directly. Log::shouldReceive would
 * only prove the listener called the logger; spying on the channel proves the
 * message and its context are what an on-call engineer would actually see.
 */

it('logs a failed login with the submitted address', function () {
    Log::shouldReceive('channel')->with('security')->andReturnSelf();
    Log::shouldReceive('log')
        ->once()
        ->withArgs(function (string $level, string $message, array $context) {
            return $level === 'warning'
                && $message === 'Failed login attempt'
                && $context['email'] === 'attacker@example.com'
                && $context['user_exists'] === false;
        });

    event(new Failed('web', null, [
        'email' => 'attacker@example.com',
        'password' => 'hunter2',
    ]));
});

it('never writes the submitted password to the log', function () {
    $captured = [];

    Log::shouldReceive('channel')->with('security')->andReturnSelf();
    Log::shouldReceive('log')->andReturnUsing(function ($level, $message, $context) use (&$captured) {
        $captured[] = json_encode($context);
    });

    event(new Failed('web', null, [
        'email' => 'someone@example.com',
        'password' => 'SuperSecret-Password-123!',
    ]));

    expect(implode(' ', $captured))
        ->not->toContain('SuperSecret-Password-123!')
        ->not->toContain('password');
});

it('distinguishes a real account from an unknown one', function () {
    $user = panelUser();
    $seen = [];

    Log::shouldReceive('channel')->with('security')->andReturnSelf();
    Log::shouldReceive('log')->andReturnUsing(function ($level, $message, $context) use (&$seen) {
        $seen[] = $context['user_exists'] ?? null;
    });

    event(new Failed('web', $user, ['email' => $user->email, 'password' => 'wrong']));
    event(new Failed('web', null, ['email' => 'ghost@example.com', 'password' => 'wrong']));

    expect($seen)->toBe([true, false]);
});

it('records a failed login against a real account in the audit trail', function () {
    $user = panelUser();

    event(new Failed('web', $user, ['email' => $user->email, 'password' => 'wrong']));

    expect(AuditLog::where('event', 'auth.login_failed')->where('user_id', $user->id)->exists())
        ->toBeTrue();
});

it('logs a lockout at error level and audits it', function () {
    Log::shouldReceive('channel')->with('security')->andReturnSelf();
    Log::shouldReceive('log')
        ->once()
        ->withArgs(fn (string $level, string $message) => $level === 'error'
            && $message === 'Login lockout triggered');

    $request = Request::create('/login', 'POST', ['email' => 'target@example.com']);

    event(new Lockout($request));

    expect(AuditLog::where('event', 'auth.lockout')->exists())->toBeTrue();
});

it('records a successful login and stamps last_login', function () {
    $user = panelUser();

    expect($user->last_login_at)->toBeNull();

    event(new Login('web', $user, false));

    $user->refresh();

    expect($user->last_login_at)->not->toBeNull()
        ->and(AuditLog::where('event', 'auth.login')->where('user_id', $user->id)->exists())->toBeTrue();
});

it('treats a completed password reset as a privileged change', function () {
    $user = panelUser();

    Log::shouldReceive('channel')->with('security')->andReturnSelf();
    Log::shouldReceive('log')
        ->once()
        ->withArgs(fn (string $level, string $message) => $level === 'warning'
            && $message === 'Password reset completed');

    event(new PasswordReset($user));

    expect($user->fresh()->password_changed_at)->not->toBeNull()
        ->and(AuditLog::where('event', 'auth.password_reset')->exists())->toBeTrue();
});

it('strips control characters from a crafted email before logging it', function () {
    $captured = null;

    Log::shouldReceive('channel')->with('security')->andReturnSelf();
    Log::shouldReceive('log')->andReturnUsing(function ($level, $message, $context) use (&$captured) {
        $captured = $context['email'] ?? '';
    });

    // A newline here would let an attacker forge an extra log line.
    event(new Failed('web', null, [
        'email' => "victim@example.com\n2026-01-01 00:00:00 ADMIN LOGIN SUCCESS",
        'password' => 'x',
    ]));

    expect($captured)->not->toContain("\n")->not->toContain("\r");
});

it('caps a pathologically long email so it cannot bloat the log', function () {
    $captured = null;

    Log::shouldReceive('channel')->with('security')->andReturnSelf();
    Log::shouldReceive('log')->andReturnUsing(function ($level, $message, $context) use (&$captured) {
        $captured = $context['email'] ?? '';
    });

    event(new Failed('web', null, [
        'email' => str_repeat('a', 5000).'@example.com',
        'password' => 'x',
    ]));

    expect(mb_strlen((string) $captured))->toBeLessThanOrEqual(190);
});

it('handles a login attempt that supplied no email at all', function () {
    $captured = null;

    Log::shouldReceive('channel')->with('security')->andReturnSelf();
    Log::shouldReceive('log')->andReturnUsing(function ($level, $message, $context) use (&$captured) {
        $captured = $context['email'] ?? null;
    });

    event(new Failed('web', null, ['password' => 'x']));

    expect($captured)->toBe('(none supplied)');
});
