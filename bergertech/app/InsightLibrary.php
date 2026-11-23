<?php

namespace App;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * Reads the long-form guides in config/insights.php and adds what the views
 * need on top of the raw content: the slug and an estimated reading time.
 */
class InsightLibrary
{
    /** Average adult reading speed, in words per minute. */
    public const WORDS_PER_MINUTE = 200;

    /**
     * Every insight, in the order they are configured.
     *
     * @return Collection<string, array{slug: string, title: string, category: string, excerpt: string, image: string, takeaways: list<string>, sections: list<array{heading: string, paragraphs: list<string>, points?: list<string>}>, read_minutes: int}>
     */
    public function all(): Collection
    {
        return collect(config('insights'))
            ->map(fn (array $insight, string $slug): array => $this->prepare($slug, $insight));
    }

    /**
     * One insight by slug, or null when there is no such insight.
     *
     * @return array{slug: string, title: string, category: string, excerpt: string, image: string, takeaways: list<string>, sections: list<array{heading: string, paragraphs: list<string>, points?: list<string>}>, read_minutes: int}|null
     */
    public function find(string $slug): ?array
    {
        return $this->all()->get($slug);
    }

    /**
     * @param  array{title: string, category: string, excerpt: string, image: string, takeaways: list<string>, sections: list<array{heading: string, paragraphs: list<string>, points?: list<string>}>}  $insight
     * @return array{slug: string, title: string, category: string, excerpt: string, image: string, takeaways: list<string>, sections: list<array{heading: string, paragraphs: list<string>, points?: list<string>}>, read_minutes: int}
     */
    private function prepare(string $slug, array $insight): array
    {
        $words = collect($insight['sections'])
            ->flatMap(fn (array $section): array => [$section['heading'], ...$section['paragraphs'], ...($section['points'] ?? [])])
            ->sum(fn (string $text): int => Str::wordCount($text));

        return [
            'slug' => $slug,
            ...$insight,
            'read_minutes' => max(1, (int) round($words / self::WORDS_PER_MINUTE)),
        ];
    }
}
