@props([
    'happening' => config('site.happening'),
])

@php
    $insights = app(\App\InsightLibrary::class);

    // The pastel pairs from the industries section, cycled per item for its tag and button.
    $tints = [
        'bg-sky text-sky-ink',
        'bg-lilac text-lilac-ink',
        'bg-mint text-mint-ink',
        'bg-blush text-blush-ink',
        'bg-sand text-sand-ink',
    ];

    // Fill insight-backed items from config/insights.php; drop any whose slug no longer exists.
    $items = collect($happening['items'])
        ->map(function (array $item) use ($insights): ?array {
            if (isset($item['insight'])) {
                $insight = $insights->find($item['insight']);

                if ($insight === null) {
                    return null;
                }

                return $item + [
                    'label' => $insight['title'],
                    'category' => $insight['category'],
                    'text' => $insight['excerpt'],
                    'image' => $insight['image'],
                    'url' => route('insights.show', $insight['slug']),
                ];
            }

            return $item + ['url' => route($item['url_name'])];
        })
        ->filter()
        ->values()
        ->map(fn (array $item, int $index): array => $item + ['tint' => $tints[$index % count($tints)]]);
@endphp

{{--
    Expanding gallery: the active card is wide with its label on it, the rest
    shrink to strips that get narrower the further they are from it. Clicking a
    strip or an arrow expands it and swaps the caption below. Widths come from
    data-distance (see .happening-panel in app.css); resources/js/happening.js
    keeps it in step. Without JS the first item simply stays open.
--}}
<section id="whats-happening" data-happening {{ $attributes->class(['py-20 font-display sm:py-28']) }}>
    <div class="container-page">
        <div class="flex items-end justify-between gap-6">
            <x-section-heading
                :title="$happening['title']"
                :highlight="$happening['highlight'] ?? null"
                :lead="$happening['subtitle']"
                align="left"
                :soft="true"
            />

            <div class="flex shrink-0 gap-3">
                <button type="button" data-happening-prev disabled
                        class="leaf-diag flex h-11 w-11 items-center justify-center bg-lilac text-lilac-ink transition hover:brightness-95 disabled:cursor-default disabled:opacity-40 disabled:hover:brightness-100 sm:h-12 sm:w-12"
                        aria-label="Previous">
                    <x-icon name="chevron-right" class="h-5 w-5 rotate-180" />
                </button>
                <button type="button" data-happening-next @disabled($items->count() < 2)
                        class="leaf-diag flex h-11 w-11 items-center justify-center bg-lilac text-lilac-ink transition hover:brightness-95 disabled:cursor-default disabled:opacity-40 disabled:hover:brightness-100 sm:h-12 sm:w-12"
                        aria-label="Next">
                    <x-icon name="chevron-right" class="h-5 w-5" />
                </button>
            </div>
        </div>

        <div class="mt-10 flex h-[340px] gap-2 sm:mt-12 sm:h-[440px] sm:gap-3 lg:h-[520px] lg:gap-4">
            @foreach ($items as $item)
                <button type="button" data-happening-panel data-distance="{{ min($loop->index, 4) }}"
                        @class(['happening-panel relative overflow-hidden bg-brand-950 text-left', 'is-active' => $loop->first])
                        aria-label="Show: {{ $item['label'] }}" aria-pressed="{{ $loop->first ? 'true' : 'false' }}"
                        aria-controls="happening-caption-{{ $loop->index }}">
                    <img src="{{ asset($item['image']) }}" alt=""
                         class="absolute inset-0 h-full w-full object-cover"
                         loading="lazy" decoding="async">
                    <span class="happening-scrim absolute inset-0 bg-gradient-to-t from-brand-950/90 via-brand-950/40 via-45% to-transparent" aria-hidden="true"></span>

                    <span class="happening-label absolute inset-x-0 bottom-0 p-5 sm:p-8 lg:p-10 lg:pr-40" aria-hidden="true">
                        @if (! empty($item['category']))
                            <span class="mb-4 inline-block rounded-full px-3 py-1 text-[12.5px] font-medium {{ $item['tint'] }}">{{ $item['category'] }}</span>
                        @endif
                        <span class="block max-w-xl text-2xl leading-[1.1] font-semibold tracking-tight text-white sm:text-4xl lg:text-[44px]">{{ $item['label'] }}</span>
                        <img src="{{ asset(config('site.logo_footer')) }}" alt="" class="absolute right-10 bottom-11 hidden h-6 w-auto opacity-90 lg:block" width="1977" height="425" loading="lazy" decoding="async">
                    </span>
                </button>
            @endforeach
        </div>

        <div class="mt-8 sm:mt-10" aria-live="polite">
            @foreach ($items as $item)
                <div id="happening-caption-{{ $loop->index }}" data-happening-caption @if (! $loop->first) hidden @endif
                     class="happening-caption flex flex-col gap-6 md:flex-row md:items-start md:justify-between md:gap-12">
                    <p class="max-w-3xl text-lg leading-relaxed text-muted sm:text-xl">
                        <span class="font-semibold text-ink">{{ $item['lead'] }}</span>
                        {{ $item['text'] }}
                    </p>
                    <a href="{{ $item['url'] }}"
                       class="group inline-flex shrink-0 items-center gap-2 self-start rounded-full px-6 py-3 text-[15px] font-semibold transition hover:brightness-95 {{ $item['tint'] }}">
                        {{ $item['cta'] }}
                        <x-icon name="chevron-right" class="h-4 w-4 transition group-hover:translate-x-0.5" />
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</section>
