<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Gates every panel page behind a confirmed second factor.
 *
 * Two distinct states are handled:
 *   - no secret yet      -> push the user into enrolment, nowhere else
 *   - secret, not challenged this session -> push to the challenge
 *
 * The session flag is cleared on logout and on password change (Filament's
 * AuthenticateSession handles the latter), so it cannot outlive the session
 * it was granted in.
 */
class RequireTwoFactor
{
    public const SESSION_KEY = 'auth.two_factor_passed_at';

    public function handle(Request $request, Closure $next): Response
    {
        if (! config('security.two_factor.enforce')) {
            return $next($request);
        }

        $user = $request->user();

        if (! $user) {
            return $next($request);
        }

        $setupRoute = 'filament.admin.pages.two-factor-setup';
        $challengeRoute = 'filament.admin.pages.two-factor-challenge';

        // Allow the enrolment/challenge pages themselves plus logout, or the
        // user would be redirected in a loop.
        if ($request->routeIs($setupRoute, $challengeRoute, 'filament.admin.auth.logout')) {
            return $next($request);
        }

        if (! $user->hasTwoFactorEnabled()) {
            return redirect()->route($setupRoute);
        }

        if (! $request->session()->has(self::SESSION_KEY)) {
            return redirect()->route($challengeRoute);
        }

        return $next($request);
    }
}
