<div class="grid grid-cols-[300px_1fr]">
    <div class="border-r border-line bg-surface p-6">
        <p class="text-[11px] font-bold tracking-[0.16em] text-muted uppercase">Resources</p>

        <ul class="mt-3 space-y-1">
            @foreach ([
                ['icon' => 'book', 'label' => 'Insights', 'text' => 'Guides on ERP, cloud and automation', 'url' => route('insights.index')],
                ['icon' => 'sparkle', 'label' => 'Resource Center', 'text' => 'Everything in one place', 'url' => route('resources')],
                ['icon' => 'help', 'label' => 'FAQ', 'text' => 'Quick answers to common questions', 'url' => route('resources').'#faq'],
                ['icon' => 'calendar', 'label' => 'Book a consultation', 'text' => config('site.booking.duration').' minutes with a consultant', 'url' => route('appointment')],
            ] as $link)
                <li>
                    <a href="{{ $link['url'] }}" class="group flex items-center gap-3 rounded-xl p-2.5 transition hover:bg-white hover:shadow-card">
                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-white text-brand-600 ring-1 ring-line transition group-hover:bg-brand-600 group-hover:text-white group-hover:ring-brand-600">
                            <x-icon :name="$link['icon']" class="h-[18px] w-[18px]" />
                        </span>
                        <span class="min-w-0">
                            <span class="block font-display text-[14px] font-bold text-ink">{{ $link['label'] }}</span>
                            <span class="block text-[12.5px] text-muted">{{ $link['text'] }}</span>
                        </span>
                    </a>
                </li>
            @endforeach
        </ul>
    </div>

    <div class="p-6">
        <div class="flex items-center justify-between">
            <p class="text-[11px] font-bold tracking-[0.16em] text-muted uppercase">Latest insights</p>
            <a href="{{ route('insights.index') }}" class="link-arrow text-[11px]">
                All insights
                <x-icon name="arrow" class="h-3.5 w-3.5" />
            </a>
        </div>

        <ul class="mt-4 grid grid-cols-2 gap-5">
            @foreach (app(\App\InsightLibrary::class)->all()->take(2) as $insight)
                <li>
                    <a href="{{ route('insights.show', $insight['slug']) }}" class="group block">
                        <span class="block aspect-[16/9] overflow-hidden rounded-xl">
                            <img src="{{ asset($insight['image']) }}" alt=""
                                 class="h-full w-full object-cover transition duration-500 group-hover:scale-105"
                                 loading="lazy" decoding="async">
                        </span>
                        <span class="mt-3 block text-[11px] font-bold tracking-[0.12em] text-brand-600 uppercase">
                            {{ $insight['category'] }} · {{ $insight['read_minutes'] }} min read
                        </span>
                        <span class="mt-1 block font-display text-[15px] leading-snug font-bold text-ink transition group-hover:text-brand-600">{{ $insight['title'] }}</span>
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
</div>
