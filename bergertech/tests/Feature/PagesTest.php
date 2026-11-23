<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class PagesTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_home_page_renders_hero_and_both_catalogues(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Simplifying Business')
            ->assertSee('Our Services')
            ->assertSee('Our Solutions')
            ->assertSee('Our Story');
    }

    public function test_about_page_renders(): void
    {
        $this->get('/about')
            ->assertOk()
            ->assertSee('Why We Are Premier Choice');
    }

    public function test_resources_page_links_every_solution_brief_and_answers_the_faq(): void
    {
        $response = $this->get('/resources')
            ->assertOk()
            ->assertSee('Resource Center')
            ->assertSee(config('site.faq.0.question'));

        foreach (array_keys(config('solutions')) as $slug) {
            $response->assertSee(route('solutions.show', $slug), false);
        }
    }

    public function test_resources_page_is_reachable_from_the_main_navigation(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('href="'.route('resources').'"', false);
    }

    public function test_service_index_and_every_configured_service_render(): void
    {
        $this->get('/services')->assertOk()->assertSee('Our Core Services');

        foreach (config('services_content') as $slug => $service) {
            $this->get("/services/{$slug}")
                ->assertOk()
                ->assertSee($service['hero_title'], false)
                ->assertSee($service['intro_title'], false);
        }
    }

    public function test_solution_index_and_every_configured_solution_render(): void
    {
        $this->get('/solutions')->assertOk()->assertSee('Our Solutions');

        foreach (config('solutions') as $slug => $solution) {
            $this->get("/solutions/{$slug}")
                ->assertOk()
                ->assertSee($solution['name'], false);
        }
    }

    public function test_unknown_service_or_solution_returns_404(): void
    {
        $this->get('/services/not-a-service')->assertNotFound();
        $this->get('/solutions/not-a-solution')->assertNotFound();
    }

    public function test_dotted_slug_returns_404_instead_of_reading_nested_config(): void
    {
        $service = array_key_first(config('services_content'));
        $solution = array_key_first(config('solutions'));

        $this->get("/services/{$service}.services")->assertNotFound();
        $this->get("/services/{$service}.why")->assertNotFound();
        $this->get("/solutions/{$solution}.sections")->assertNotFound();
        $this->get("/solutions/{$solution}.hero_gradient")->assertNotFound();
    }

    public function test_solution_index_links_to_every_solution_page(): void
    {
        $response = $this->get('/solutions')->assertOk();

        foreach (array_keys(config('solutions')) as $slug) {
            $response->assertSee(route('solutions.show', $slug), false);
        }
    }

    public function test_every_page_renders_the_footer_contact_details(): void
    {
        foreach (['/', '/about', '/services', '/solutions', '/contact', '/appointment'] as $path) {
            $this->get($path)
                ->assertOk()
                ->assertSee(config('site.contact.email'))
                ->assertSee(config('site.contact.phone'));
        }
    }
}
