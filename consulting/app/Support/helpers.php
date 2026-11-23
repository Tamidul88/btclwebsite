<?php

if (! function_exists('csp_nonce')) {
    /**
     * The per-request CSP nonce set by SecurityHeaders.
     *
     * Returns an empty string if the middleware has not run (e.g. console),
     * so views never fatal outside the HTTP lifecycle.
     */
    function csp_nonce(): string
    {
        return app()->bound('csp-nonce') ? app('csp-nonce') : '';
    }
}
