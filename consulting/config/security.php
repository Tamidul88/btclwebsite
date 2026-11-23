<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Admin panel path
    |--------------------------------------------------------------------------
    | Never "/admin". Obscurity is not a control on its own, but it removes the
    | site from every commodity scanner's wordlist, which cuts log noise and
    | credential-stuffing volume dramatically. The real controls are below.
    */

    'admin_path' => env('ADMIN_PATH', 'ctrl-a9f2'),

    /*
    |--------------------------------------------------------------------------
    | Admin IP allowlist
    |--------------------------------------------------------------------------
    | Comma-separated IPs and/or CIDR ranges. Empty disables the check.
    | Trade-off: an allowlist locks out remote admins on dynamic IPs. Leave it
    | empty unless the team has static egress (office or VPN), in which case it
    | is the single highest-value control on this panel.
    */

    'admin_ip_allowlist' => array_values(array_filter(
        array_map('trim', explode(',', (string) env('ADMIN_IP_ALLOWLIST', '')))
    )),

    /*
    |--------------------------------------------------------------------------
    | Login lockout
    |--------------------------------------------------------------------------
    */

    'login' => [
        'max_attempts' => (int) env('LOGIN_MAX_ATTEMPTS', 5),
        'decay_minutes' => (int) env('LOGIN_DECAY_MINUTES', 15),
    ],

    /*
    |--------------------------------------------------------------------------
    | Two-factor authentication
    |--------------------------------------------------------------------------
    | Enforced for every panel user. 'window' allows one 30s step either side
    | of the current one to tolerate clock drift; higher values widen the
    | replay window for a stolen code.
    */

    'two_factor' => [
        'enforce' => (bool) env('TWO_FACTOR_ENFORCE', true),
        'window' => (int) env('TWO_FACTOR_WINDOW', 1),
        'recovery_code_count' => 8,
    ],

    /*
    |--------------------------------------------------------------------------
    | Content Security Policy
    |--------------------------------------------------------------------------
    | The public site runs a strict nonce CSP with no 'unsafe-inline' and no
    | 'unsafe-eval'; that is why Alpine's CSP build is used (see resources/js).
    |
    | Filament needs 'unsafe-eval' (Alpine's standard build) and 'unsafe-inline'
    | styles. Rather than weaken the whole site, the relaxed policy is scoped to
    | the admin panel only, which additionally sits behind auth, 2FA, an
    | optional IP allowlist and a short session.
    */

    'csp' => [
        'report_only' => (bool) env('CSP_REPORT_ONLY', false),
        'report_uri' => env('CSP_REPORT_URI'),
    ],

    /*
    |--------------------------------------------------------------------------
    | HSTS
    |--------------------------------------------------------------------------
    | Only emitted over HTTPS. Do not enable preload until the site has served
    | HTTPS-only on every subdomain for a while: preload is very hard to undo.
    */

    'hsts' => [
        'enabled' => (bool) env('HSTS_ENABLED', true),
        'max_age' => (int) env('HSTS_MAX_AGE', 31536000),
        'include_subdomains' => (bool) env('HSTS_INCLUDE_SUBDOMAINS', true),
        'preload' => (bool) env('HSTS_PRELOAD', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Uploads
    |--------------------------------------------------------------------------
    | Attachments are stored on the private disk, never under public/. Images
    | are re-encoded (stripping EXIF and any appended payload); PDFs are
    | accepted by signature only and never rendered inline.
    */

    'uploads' => [
        'max_kb' => (int) env('UPLOAD_MAX_KB', 5120),
        'mimes' => ['application/pdf', 'image/jpeg', 'image/png', 'image/webp'],
        'extensions' => ['pdf', 'jpg', 'jpeg', 'png', 'webp'],
        'image_mimes' => ['image/jpeg', 'image/png', 'image/webp'],
        'max_image_dimension' => 3000,
    ],
];
