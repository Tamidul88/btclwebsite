<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

/**
 * Emits the security header set on every response.
 *
 * A per-request nonce is generated before the view renders so Blade can stamp
 * it onto the one inline script the public site uses. The admin panel gets a
 * deliberately looser policy; see config/security.php for why.
 */
class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        // Generated up-front so views can read it via the `csp_nonce()` helper.
        $nonce = Str::random(24);
        app()->instance('csp-nonce', $nonce);

        $response = $next($request);

        // Streamed/binary responses (file downloads) get the header set but no
        // CSP work, since there is no document to constrain.
        $isAdmin = $request->is(config('security.admin_path').'*');

        $this->applyCsp($response, $nonce, $isAdmin);

        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Cross-Origin-Opener-Policy', 'same-origin');
        $response->headers->set('Cross-Origin-Resource-Policy', 'same-origin');
        $response->headers->set('X-Permitted-Cross-Domain-Policies', 'none');

        $response->headers->set('Permissions-Policy', implode(', ', [
            'accelerometer=()', 'autoplay=()', 'camera=()', 'display-capture=()',
            'encrypted-media=()', 'fullscreen=(self)', 'geolocation=()',
            'gyroscope=()', 'magnetometer=()', 'microphone=()', 'midi=()',
            'payment=()', 'usb=()', 'xr-spatial-tracking=()',
            'interest-cohort=()',
        ]));

        if ($request->secure() && config('security.hsts.enabled')) {
            $hsts = 'max-age='.config('security.hsts.max_age');

            if (config('security.hsts.include_subdomains')) {
                $hsts .= '; includeSubDomains';
            }

            if (config('security.hsts.preload')) {
                $hsts .= '; preload';
            }

            $response->headers->set('Strict-Transport-Security', $hsts);
        }

        /*
         * PHP adds X-Powered-By at the SAPI level from expose_php, so removing
         * it from the Symfony bag is not enough — header_remove() is required.
         * The server guide also sets expose_php = Off, which is the real fix;
         * this is the belt to that braces.
         */
        $response->headers->remove('X-Powered-By');

        if (! headers_sent()) {
            header_remove('X-Powered-By');
        }

        return $response;
    }

    private function applyCsp(Response $response, string $nonce, bool $isAdmin): void
    {
        $directives = [
            'default-src' => ["'self'"],
            'base-uri' => ["'self'"],
            'form-action' => ["'self'"],
            'frame-ancestors' => ["'none'"],
            'object-src' => ["'none'"],
            'img-src' => ["'self'", 'data:'],
            'font-src' => ["'self'", 'data:'],
            'connect-src' => ["'self'"],
            'manifest-src' => ["'self'"],
            'media-src' => ["'self'"],
            'worker-src' => ["'self'", 'blob:'],
            'upgrade-insecure-requests' => [],
        ];

        if ($isAdmin) {
            // Filament bundles Alpine's standard build, which compiles element
            // expressions with new Function() — that requires 'unsafe-eval'.
            // Scoped here only, behind auth + 2FA + optional IP allowlist.
            $directives['script-src'] = ["'self'", "'unsafe-eval'", "'nonce-{$nonce}'"];
            $directives['style-src'] = ["'self'", "'unsafe-inline'"];
        } else {
            // Public site: no unsafe-inline, no unsafe-eval. The single inline
            // script carries the nonce; Alpine runs from its CSP build.
            $directives['script-src'] = ["'self'", "'nonce-{$nonce}'"];
            $directives['style-src'] = ["'self'", "'nonce-{$nonce}'"];
        }

        if ($reportUri = config('security.csp.report_uri')) {
            $directives['report-uri'] = [$reportUri];
        }

        $policy = collect($directives)
            ->map(fn (array $values, string $key) => $values === []
                ? $key
                : $key.' '.implode(' ', $values))
            ->implode('; ');

        $header = config('security.csp.report_only')
            ? 'Content-Security-Policy-Report-Only'
            : 'Content-Security-Policy';

        $response->headers->set($header, $policy);
    }
}
