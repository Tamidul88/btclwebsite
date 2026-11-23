<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Vite;
use Tests\TestCase;

class SecurityHeadersTest extends TestCase
{
    use LazilyRefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Vite::useHotFile(storage_path('framework/testing/vite-not-running'));
    }

    public function test_pages_send_browser_hardening_headers(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('X-Frame-Options', 'SAMEORIGIN')
            ->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin')
            ->assertHeader('Permissions-Policy', 'camera=(), microphone=(), geolocation=(), payment=()');
    }

    public function test_content_security_policy_only_allows_scripts_and_styles_from_this_origin_or_with_the_page_nonce(): void
    {
        $response = $this->get('/')->assertOk();
        $nonce = Vite::cspNonce();

        $this->assertSame(
            "default-src 'self'; "
            ."script-src 'self' 'nonce-{$nonce}'; "
            ."style-src 'self' 'nonce-{$nonce}' https://fonts.googleapis.com; "
            ."font-src 'self' https://fonts.gstatic.com; "
            ."img-src 'self' data:; "
            ."connect-src 'self'; "
            ."object-src 'none'; "
            ."base-uri 'self'; "
            ."form-action 'self'; "
            ."frame-ancestors 'self'",
            $response->headers->get('Content-Security-Policy'),
        );
        $response->assertSee('nonce="'.$nonce.'"', false);
    }

    public function test_content_security_policy_allows_the_vite_dev_server_while_it_is_running(): void
    {
        $hotFile = tempnam(sys_get_temp_dir(), 'vite-hot');
        file_put_contents($hotFile, "http://[::1]:5173\n");
        Vite::useHotFile($hotFile);

        try {
            $policy = $this->get('/')->assertOk()->headers->get('Content-Security-Policy');
        } finally {
            unlink($hotFile);
        }

        $this->assertStringContainsString("script-src 'self' 'nonce-".Vite::cspNonce()."' http://[::1]:5173;", $policy);
        $this->assertStringContainsString("connect-src 'self' http://[::1]:5173 ws://[::1]:5173;", $policy);
    }

    public function test_unknown_routes_also_send_security_headers(): void
    {
        $this->get('/no-such-page')
            ->assertNotFound()
            ->assertHeader('X-Frame-Options', 'SAMEORIGIN')
            ->assertHeader('Content-Security-Policy');
    }

    public function test_strict_transport_security_is_sent_only_over_https(): void
    {
        $this->get('https://localhost/')
            ->assertOk()
            ->assertHeader('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');

        $this->get('http://localhost/')
            ->assertOk()
            ->assertHeaderMissing('Strict-Transport-Security');
    }
}
