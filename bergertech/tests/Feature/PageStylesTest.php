<?php

namespace Tests\Feature;

use App\PageStyles;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Vite;
use Tests\TestCase;

class PageStylesTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_no_page_renders_a_style_attribute_that_the_content_security_policy_would_block(): void
    {
        $paths = [
            '/', '/about', '/resources', '/insights', '/services', '/solutions', '/contact', '/appointment',
            ...array_map(fn (string $slug): string => "/services/{$slug}", array_keys(config('services_content'))),
            ...array_map(fn (string $slug): string => "/solutions/{$slug}", array_keys(config('solutions'))),
            ...array_map(fn (string $slug): string => "/insights/{$slug}", array_keys(config('insights'))),
        ];

        foreach ($paths as $path) {
            $this->get($path)->assertOk()->assertDontSee(' style="', false);
        }
    }

    public function test_service_accent_is_delivered_in_the_nonced_style_block(): void
    {
        $slug = array_key_first(config('services_content'));
        $accent = config("services_content.{$slug}.accent");

        $response = $this->get("/services/{$slug}")->assertOk();

        $response->assertSee('<style nonce="'.Vite::cspNonce().'">', false);
        $response->assertSee("background: {$accent};", false);
    }

    public function test_a_value_cannot_close_the_style_element(): void
    {
        $styles = new PageStyles;
        $styles->classFor("background: red;</style><script>alert('xss')</script>");

        $this->assertStringNotContainsString('</style>', $styles->css());
        $this->assertStringNotContainsString('<script>', $styles->css());
    }
}
