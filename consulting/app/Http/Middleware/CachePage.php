<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

/**
 * Short-lived full-page cache for anonymous GET requests.
 *
 * Guarded carefully, because a page cache that serves one visitor's response
 * to another is a data-leak primitive. Caching is skipped whenever the request
 * is authenticated, is not a plain GET, carries a query string, holds flash
 * data, or does not return a clean 200.
 *
 * The CSP nonce is per-request, so cached HTML would otherwise carry a stale
 * nonce and every inline script would be blocked. The body is stored with a
 * placeholder and the live nonce is stamped back in on the way out, which
 * keeps the strict CSP working without weakening it.
 */
class CachePage
{
    private const NONCE_PLACEHOLDER = '__CSP_NONCE__';

    public function handle(Request $request, Closure $next, int $ttl = 600): Response
    {
        if (! $this->isCacheable($request)) {
            return $next($request);
        }

        $key = $this->cacheKey($request);
        $nonce = app('csp-nonce');

        if ($cached = Cache::get($key)) {
            return response(
                str_replace(self::NONCE_PLACEHOLDER, $nonce, $cached['body']),
                200,
                ['Content-Type' => $cached['content_type']],
            )->header('X-Page-Cache', 'HIT');
        }

        $response = $next($request);

        if ($this->isStorable($request, $response)) {
            Cache::put($key, [
                'body' => str_replace($nonce, self::NONCE_PLACEHOLDER, (string) $response->getContent()),
                'content_type' => $response->headers->get('Content-Type', 'text/html; charset=UTF-8'),
            ], $ttl);
        }

        return $response->header('X-Page-Cache', 'MISS');
    }

    /** Only plain, anonymous, unparameterised GETs are ever read from cache. */
    private function isCacheable(Request $request): bool
    {
        return $request->isMethod('GET')
            && $request->user() === null
            && $request->query->count() === 0;
    }

    /**
     * A response is storable only if it is a clean HTML 200 with no flash
     * data — a validation-error or success page must never be shared.
     */
    private function isStorable(Request $request, Response $response): bool
    {
        if ($response->getStatusCode() !== 200) {
            return false;
        }

        if (! str_contains((string) $response->headers->get('Content-Type'), 'text/html')) {
            return false;
        }

        if ($request->hasSession()) {
            $session = $request->session();

            if ($session->has('status') || $session->has('errors')) {
                return false;
            }
        }

        return true;
    }

    /**
     * The key includes a fingerprint of the Vite manifest.
     *
     * Without it, a deploy that produces new asset hashes leaves the cache
     * serving HTML that references the *previous* build's CSS and JS — those
     * files are gone, so the page renders unstyled until the TTL expires.
     * Folding the manifest into the key makes a new build invalidate the cache
     * automatically, rather than relying on someone remembering to flush it.
     */
    private function cacheKey(Request $request): string
    {
        return 'page:'.sha1(implode('|', [
            $request->getSchemeAndHttpHost(),
            $request->getPathInfo(),
            $this->assetFingerprint(),
        ]));
    }

    private function assetFingerprint(): string
    {
        static $fingerprint = null;

        if ($fingerprint !== null) {
            return $fingerprint;
        }

        $manifest = public_path('build/manifest.json');

        return $fingerprint = is_file($manifest)
            ? (string) filemtime($manifest)
            : 'dev';
    }
}
