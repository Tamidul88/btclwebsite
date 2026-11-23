<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class ImagesTest extends TestCase
{
    use LazilyRefreshDatabase;

    /**
     * Every "images/..." string in the content configs must exist on disk.
     */
    public function test_every_configured_image_path_exists(): void
    {
        $missing = [];

        $walk = function (array $data, string $path) use (&$walk, &$missing) {
            foreach ($data as $key => $value) {
                if (is_array($value)) {
                    $walk($value, "{$path}.{$key}");

                    continue;
                }

                if (is_string($value) && str_starts_with($value, 'images/')
                    && ! file_exists(public_path($value))) {
                    $missing[] = "{$path}.{$key} => {$value}";
                }
            }
        };

        foreach (['site', 'solutions', 'services_content'] as $file) {
            $walk(config($file), $file);
        }

        $this->assertSame([], $missing, 'Configured images missing from public/: '.implode(', ', $missing));
    }

    /**
     * Catches images hard-coded in Blade as well as the config-driven ones.
     */
    public function test_every_rendered_image_resolves_to_a_file(): void
    {
        $paths = ['/', '/about', '/services', '/solutions', '/contact', '/appointment'];

        foreach (array_keys(config('services_content')) as $slug) {
            $paths[] = "/services/{$slug}";
        }
        foreach (array_keys(config('solutions')) as $slug) {
            $paths[] = "/solutions/{$slug}";
        }

        $missing = [];
        $seen = 0;

        foreach ($paths as $path) {
            $html = $this->get($path)->assertOk()->getContent();

            preg_match_all('#src="[^"]*?/(images/[^"]+)"#', $html, $matches);

            foreach (array_unique($matches[1]) as $image) {
                $seen++;
                if (! file_exists(public_path($image))) {
                    $missing[] = "{$path}: {$image}";
                }
            }
        }

        $this->assertGreaterThan(40, $seen, 'Expected the pages to reference the downloaded imagery.');
        $this->assertSame([], $missing, 'Rendered images missing from public/: '.implode(', ', $missing));
    }

    public function test_the_header_and_footer_render_the_real_logo(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee(config('site.logo'), false);

        $this->get('/')
            ->assertSee(config('site.logo_footer'), false);
    }

    public function test_the_home_hero_image_is_eagerly_loaded(): void
    {
        $html = $this->get('/')->assertOk()->getContent();

        $this->assertMatchesRegularExpression(
            '#<img[^>]+'.preg_quote(config('site.hero.image'), '#').'[^>]+fetchpriority="high"#s',
            $html,
            'The largest-contentful hero image should not be lazy loaded.'
        );
    }
}
