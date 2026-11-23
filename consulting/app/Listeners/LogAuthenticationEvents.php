<?php

namespace App\Listeners;

use App\Models\User;
use App\Services\AuditLogger;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;

/**
 * Writes every authentication outcome to the security log and, where there is
 * an identifiable actor, to the audit trail.
 *
 * Two rules govern what is recorded:
 *
 *  1. Never log the submitted password, not even its length. A log that can
 *     be read by ops staff or shipped to a SIEM must not become a secondary
 *     credential store.
 *
 *  2. On a failed attempt, log the *submitted* email verbatim. It is the only
 *     way to distinguish "one person fat-fingering their password" from
 *     "someone spraying a list of addresses", and that distinction is the
 *     whole point of watching failures.
 */
class LogAuthenticationEvents
{
    public function __construct(private readonly AuditLogger $audit) {}

    public function onFailed(Failed $event): void
    {
        $email = $this->emailFrom($event->credentials);

        $this->security('warning', 'Failed login attempt', [
            'email' => $email,
            'guard' => $event->guard,
            // True when the address exists but the password was wrong, false
            // when no such account exists. Useful for triage; never exposed
            // to the client, which sees one generic message either way.
            'user_exists' => $event->user !== null,
        ]);

        // Recorded against the account being targeted, when it is a real one.
        if ($event->user instanceof User) {
            $this->audit->log('auth.login_failed', $event->user, [
                'email' => $email,
            ], $event->user);
        }
    }

    /**
     * Fired when the throttle limit trips. This is the signal that matters —
     * a single failure is noise, a lockout is someone trying repeatedly.
     */
    public function onLockout(Lockout $event): void
    {
        $email = $this->emailFrom($event->request->only('email'));

        $this->security('error', 'Login lockout triggered', [
            'email' => $email,
            'threshold' => config('security.login.max_attempts'),
            'window_minutes' => config('security.login.decay_minutes'),
        ]);

        $this->audit->log('auth.lockout', null, ['email' => $email]);
    }

    public function onLogin(Login $event): void
    {
        $user = $event->user;

        if ($user instanceof User) {
            $user->forceFill([
                'last_login_at' => now(),
                'last_login_ip' => request()->ip(),
            ])->save();

            $this->security('info', 'Successful login', [
                'user_id' => $user->getKey(),
                'email' => $user->email,
            ]);

            $this->audit->log('auth.login', $user, [], $user);
        }
    }

    public function onLogout(Logout $event): void
    {
        if ($event->user instanceof User) {
            $this->audit->log('auth.logout', $event->user, [], $event->user);
        }
    }

    /**
     * A password reset is a privileged change: it is the standard way an
     * account takeover is finalised, so it is logged at warning level.
     */
    public function onPasswordReset(PasswordReset $event): void
    {
        $user = $event->user;

        if ($user instanceof User) {
            $user->forceFill(['password_changed_at' => now()])->save();

            $this->security('warning', 'Password reset completed', [
                'user_id' => $user->getKey(),
                'email' => $user->email,
            ]);

            $this->audit->log('auth.password_reset', $user, [], $user);
        }
    }

    /**
     * Explicit event map.
     *
     * The methods are named on* rather than handle*, deliberately: Laravel
     * auto-discovers listener methods matching `handle*` in app/Listeners,
     * which would register every one of these a second time on top of the
     * Event::subscribe() call in AppServiceProvider — and each event would
     * then be logged and audited twice.
     *
     * @return array<class-string, string>
     */
    public function subscribe(): array
    {
        return [
            Failed::class => 'onFailed',
            Lockout::class => 'onLockout',
            Login::class => 'onLogin',
            Logout::class => 'onLogout',
            PasswordReset::class => 'onPasswordReset',
        ];
    }

    /**
     * @param  array<string, mixed>  $context
     */
    private function security(string $level, string $message, array $context = []): void
    {
        /** @var Request|null $request */
        $request = request();

        logger()->channel('security')->log($level, $message, $context + [
            'ip' => $request?->ip(),
            'user_agent' => mb_substr((string) $request?->userAgent(), 0, 200),
            'at' => now()->toIso8601String(),
        ]);
    }

    /**
     * @param  array<string, mixed>  $credentials
     */
    private function emailFrom(array $credentials): string
    {
        $email = $credentials['email'] ?? null;

        if (! is_string($email) || $email === '') {
            return '(none supplied)';
        }

        // Truncated and stripped of control characters so a crafted value
        // cannot forge a log line or blow up the log file.
        return mb_substr(
            (string) preg_replace('/[\x00-\x1F\x7F]/u', '', $email),
            0,
            190,
        );
    }
}
