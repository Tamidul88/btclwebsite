<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Vite;
use Symfony\Component\HttpFoundation\Response;

/**
 * Adds browser hardening headers to every response and hides the PHP version.
 *
 * Scripts and styles must come from this origin or carry the per-request
 * nonce, which Vite (and Boost's browser logger) add to the tags they render.
 * Views never write style="…" attributes: per-page values go through
 * PageStyles into the layout's nonced <style> block instead.
 */
class SecurityHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $nonce = Vite::useCspNonce();

        $response = $next($request);

        header_remove('X-Powered-By');

        $response->headers->add([
            'Content-Security-Policy' => $this->contentSecurityPolicy($nonce),
            'X-Content-Type-Options' => 'nosniff',
            'X-Frame-Options' => 'SAMEORIGIN',
            'Referrer-Policy' => 'strict-origin-when-cross-origin',
            'Permissions-Policy' => 'camera=(), microphone=(), geolocation=(), payment=()',
        ]);

        if ($request->isSecure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        return $response;
    }

    private function contentSecurityPolicy(string $nonce): string
    {
        $devServer = $this->viteDevServer();
        $devSocket = $devServer ? preg_replace('/^http/', 'ws', $devServer) : null;

        $directives = [
            'default-src' => ["'self'"],
            'script-src' => ["'self'", "'nonce-{$nonce}'", $devServer],
            'style-src' => ["'self'", "'nonce-{$nonce}'", 'https://fonts.googleapis.com', $devServer],
            'font-src' => ["'self'", 'https://fonts.gstatic.com', $devServer],
            'img-src' => ["'self'", 'data:', $devServer],
            'connect-src' => ["'self'", $devServer, $devSocket],
            'object-src' => ["'none'"],
            'base-uri' => ["'self'"],
            'form-action' => ["'self'"],
            'frame-ancestors' => ["'self'"],
        ];

        return collect($directives)
            ->map(fn (array $sources, string $directive): string => $directive.' '.implode(' ', array_filter($sources)))
            ->implode('; ');
    }

    /**
     * The Vite dev server origin while `npm run dev` is running, otherwise null.
     */
    private function viteDevServer(): ?string
    {
        if (! Vite::isRunningHot()) {
            return null;
        }

        return rtrim(trim(file_get_contents(Vite::hotFile())), '/');
    }
}
