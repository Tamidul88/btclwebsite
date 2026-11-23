<?php

namespace App\Providers;

use App\Listeners\LogAuthenticationEvents;
use App\Models\Enquiry;
use App\Models\User;
use App\Observers\AuditableObserver;
use App\Policies\EnquiryPolicy;
use App\Policies\UserPolicy;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->enforceHttps();
        $this->configurePasswordPolicy();
        $this->configureRateLimiting();
        $this->registerPolicies();
        $this->registerObservers();
        $this->registerValidators();
        $this->registerEventSubscribers();
    }

    /**
     * In production every generated URL is https and any http request is
     * upgraded. Belt-and-braces alongside the Nginx redirect and HSTS.
     */
    private function enforceHttps(): void
    {
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
    }

    /**
     * Strong passwords plus a breach check against Have I Been Pwned's
     * k-anonymity API — only the first five characters of the SHA-1 hash leave
     * the server, so the password itself is never transmitted.
     *
     * Trade-off: the check makes an outbound HTTPS call during registration
     * and password change. If it fails, Laravel treats the password as
     * uncompromised rather than blocking the user, so an outage degrades
     * gracefully instead of locking everyone out.
     */
    private function configurePasswordPolicy(): void
    {
        Password::defaults(function () {
            $rule = Password::min(12)
                ->letters()
                ->mixedCase()
                ->numbers()
                ->symbols();

            return $this->app->isProduction()
                ? $rule->uncompromised()
                : $rule;
        });
    }

    private function configureRateLimiting(): void
    {
        // Public forms: deliberately tight. Keyed on IP only, since these are
        // unauthenticated endpoints.
        RateLimiter::for('forms', fn (Request $request) => [
            Limit::perMinute(5)->by($request->ip()),
            Limit::perDay(30)->by($request->ip()),
        ]);

        // Panel login. Two dimensions so an attacker cannot rotate usernames
        // against one IP, or spray one account from many IPs, without tripping
        // one of the two.
        RateLimiter::for('login', function (Request $request) {
            $email = (string) $request->input('email');

            return [
                Limit::perMinutes(
                    config('security.login.decay_minutes'),
                    config('security.login.max_attempts'),
                )->by('login:ip:'.$request->ip()),

                Limit::perMinutes(
                    config('security.login.decay_minutes'),
                    config('security.login.max_attempts'),
                )->by('login:user:'.mb_strtolower($email)),
            ];
        });

        RateLimiter::for('two-factor', function (Request $request) {
            $user = $request->user();

            return Limit::perMinute(5)->by('2fa:'.($user?->getAuthIdentifier() ?? $request->ip()));
        });

        RateLimiter::for('downloads', function (Request $request) {
            $user = $request->user();

            return Limit::perMinute(30)->by($user?->getAuthIdentifier() ?? $request->ip());
        });
    }

    private function registerPolicies(): void
    {
        Gate::policy(Enquiry::class, EnquiryPolicy::class);
        Gate::policy(User::class, UserPolicy::class);

        /*
         * Deliberately NO Gate::before() admin bypass.
         *
         * A `Gate::before(fn ($user) => $user->isAdmin() ? true : null)` is a
         * common shortcut, and it silently defeats every policy rule that is
         * meant to constrain admins — "you cannot delete your own account",
         * "you cannot remove the last admin", "nobody may edit a submitted
         * enquiry". Those are exactly the rules that matter most, so each
         * policy grants admin access explicitly instead.
         *
         * Gate::after is used only to deny: it can never widen access.
         */
        Gate::after(function (User $user, string $ability, ?bool $result) {
            // A deactivated account loses everything, regardless of role,
            // without having to repeat the check in every policy method.
            if ($result === true && ! $user->is_active) {
                return false;
            }

            return $result;
        });
    }

    /**
     * Auth outcomes (failed logins, lockouts, resets) go to the security log
     * and the audit trail. Registered as a subscriber so every event is
     * wired in one place.
     */
    private function registerEventSubscribers(): void
    {
        Event::subscribe(LogAuthenticationEvents::class);
    }

    private function registerObservers(): void
    {
        Enquiry::observe(AuditableObserver::class);
        User::observe(AuditableObserver::class);
    }

    /**
     * Rejects any value containing CR or LF. Used on fields that could end up
     * near a mail header, as a second line of defence behind `email:filter`.
     */
    private function registerValidators(): void
    {
        Validator::extend('no_control_chars', function ($attribute, $value) {
            return is_string($value) && preg_match('/[\r\n\t\x00]/', $value) === 0;
        }, 'The :attribute contains characters that are not allowed.');
    }
}
