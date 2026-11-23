<?php

namespace App\Http\Middleware;

use App\Support\Cidr;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Restricts the admin panel to a configured set of IPs / CIDR ranges.
 *
 * Registered GLOBALLY and scoped internally, rather than attached to the panel
 * route group. Laravel sorts route middleware by its priority list, and
 * Authenticate sits in that list while a custom class does not — attaching
 * this to the panel would let Authenticate run first and redirect an
 * off-network probe to the login page before the IP check happened. A network
 * boundary has to be the outermost check, so it runs in the global stack.
 *
 * Disabled when the allowlist is empty, so the app still works for teams on
 * dynamic addresses. Returns 404 rather than 403 so a probe cannot confirm
 * that the panel exists at this path.
 */
class AdminIpAllowlist
{
    public function handle(Request $request, Closure $next): Response
    {
        $allowed = config('security.admin_ip_allowlist', []);

        if ($allowed === [] || ! $this->isAdminRequest($request)) {
            return $next($request);
        }

        $ip = $request->ip();

        foreach ($allowed as $range) {
            if (Cidr::matches($ip, $range)) {
                return $next($request);
            }
        }

        logger()->channel('security')->warning('Admin access blocked by IP allowlist', [
            'ip' => $ip,
            'path' => $request->path(),
        ]);

        abort(404);
    }

    private function isAdminRequest(Request $request): bool
    {
        $path = trim((string) config('security.admin_path'), '/');

        if ($path === '') {
            return false;
        }

        return $request->is($path, $path.'/*');
    }
}
