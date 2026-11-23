<?php

namespace Tests\Feature;

use App\InsightLibrary;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class InsightsTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_insights_index_lists_and_links_every_guide(): void
    {
        $response = $this->get('/insights')->assertOk();

        foreach (config('insights') as $slug => $insight) {
            $response->assertSee($insight['title'])
                ->assertSee(route('insights.show', $slug), false);
        }
    }

    public function test_every_guide_renders_its_takeaways_and_sections(): void
    {
        foreach (config('insights') as $slug => $insight) {
            $response = $this->get("/insights/{$slug}")
                ->assertOk()
                ->assertSee($insight['title'])
                ->assertSee($insight['takeaways'][0]);

            foreach ($insight['sections'] as $section) {
                $response->assertSee($section['heading']);
            }
        }
    }

    public function test_an_unknown_guide_is_not_found(): void
    {
        $this->get('/insights/not-a-real-guide')->assertNotFound();
    }

    public function test_whitepaper_tiles_on_the_home_page_link_to_their_guides(): void
    {
        $response = $this->get('/')->assertOk();

        foreach (collect(config('site.features'))->whereNotNull('insight') as $feature) {
            $response->assertSee(route('insights.show', $feature['insight']), false);
        }
    }

    public function test_whats_happening_gallery_renders_every_item_with_its_link(): void
    {
        $response = $this->get('/')->assertOk();

        foreach (config('site.happening.items') as $item) {
            if (isset($item['insight'])) {
                $response->assertSee(config("insights.{$item['insight']}.title"))
                    ->assertSee(route('insights.show', $item['insight']), false);
            } else {
                $response->assertSee($item['label'])
                    ->assertSee(route($item['url_name']), false);
            }

            $response->assertSee($item['lead']);
        }
    }

    public function test_whats_happening_gallery_skips_an_item_whose_guide_no_longer_exists(): void
    {
        config(['site.happening.items' => [
            ['insight' => 'sap-to-cloud-migration', 'lead' => 'Still published.', 'cta' => 'Read'],
            ['insight' => 'retired-guide', 'lead' => 'Taken down.', 'cta' => 'Read'],
        ]]);

        $this->get('/')
            ->assertOk()
            ->assertSee('Still published.')
            ->assertDontSee('Taken down.');
    }

    public function test_reading_time_is_estimated_from_the_word_count(): void
    {
        config(['insights' => [
            'short-guide' => [
                'title' => 'Short guide',
                'category' => 'Test',
                'excerpt' => 'Not counted.',
                'image' => 'images/story.jpg',
                'takeaways' => ['Not counted.'],
                'sections' => [
                    ['heading' => 'Heading', 'paragraphs' => [trim(str_repeat('word ', 349))], 'points' => ['fifty more words '.trim(str_repeat('x ', 47))]],
                ],
            ],
        ]]);

        // 1 + 349 + 50 = 400 words at 200 words a minute.
        $this->assertSame(2, app(InsightLibrary::class)->find('short-guide')['read_minutes']);
    }
}
