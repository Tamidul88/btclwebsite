<?php

use App\Http\Middleware\AdminIpAllowlist;
use App\Http\Middleware\CachePage;
use App\Http\Middleware\SecurityHeaders;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Applied to every response, including error pages.
        $middleware->append(SecurityHeaders::class);

        /*
         * Prepended so it is the first thing to run. It scopes itself to the
         * admin path internally; see the class docblock for why it cannot live
         * on the panel's own middleware stack.
         */
        $middleware->prepend(AdminIpAllowlist::class);

        $middleware->alias([
            'cache.page' => CachePage::class,
        ]);

        /*
         * Behind Cloudflare/nginx the real client IP arrives in a forwarded
         * header. Without this, every rate limit and the IP allowlist would see
         * the proxy address and be useless.
         *
         * TRUSTED_PROXIES must list the proxy ranges — never '*' in production,
         * because a wildcard lets any client spoof X-Forwarded-For and defeat
         * both controls.
         */
        $middleware->trustProxies(
            at: array_values(array_filter(array_map(
                'trim',
                explode(',', (string) env('TRUSTED_PROXIES', ''))
            ))) ?: null,
            headers: Request::HEADER_X_FORWARDED_FOR
                | Request::HEADER_X_FORWARDED_HOST
                | Request::HEADER_X_FORWARDED_PORT
                | Request::HEADER_X_FORWARDED_PROTO
                | Request::HEADER_X_FORWARDED_AWS_ELB,
        );

        // Encrypted cookies are the default; this documents the intent and
        // keeps the honeypot's own field readable by the package.
        $middleware->encryptCookies(except: []);

        /*
         * There is no route named "login" — authentication lives entirely in
         * the Filament panel, whose login route is namespaced and sits behind
         * the configurable admin path. Without this, the `auth` middleware
         * throws "Route [login] not defined" (a 500) instead of redirecting a
         * guest, which is a poor failure mode for an authorisation boundary.
         */
        $middleware->redirectGuestsTo(
            fn () => route('filament.admin.auth.login')
        );
    })
    ->withExceptions(function (Exceptions $exceptions) {
        /*
         * Custom error pages. Laravel renders resources/views/errors/{code}
         * automatically; this block makes sure nothing sensitive is reported
         * and that the response is a plain, non-leaky page.
         */
        $exceptions->dontReport([
            AuthenticationException::class,
            ValidationException::class,
        ]);

        // Strip request payloads from reports so credentials and message
        // bodies never reach the log or Sentry.
        $exceptions->context(fn () => []);

        $exceptions->render(function (HttpExceptionInterface $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json(
                    ['message' => $e->getStatusCode() >= 500 ? 'Server error.' : 'Request could not be completed.'],
                    $e->getStatusCode(),
                );
            }

            return null; // fall through to the Blade error view
        });
    })
    ->create();
