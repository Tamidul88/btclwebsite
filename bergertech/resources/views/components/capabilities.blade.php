@props([
    'eyebrow' => 'Capabilities',
    'title' => 'Everything We Deliver',
    'lead' => 'Seven service lines, each delivered as a service — so you can scale up or down as the business needs.',
])

@php
    /*
     * Bento layout on a three-column grid: ERP and Security take two
     * columns, the rest one, which fills three complete rows.
     */
    $wide = ['ERP as a Service', 'Security as a Service'];

    // The pastel pairs from the industries section, cycled per card for its icon blob.
    $tints = ['bg-sky text-sky-ink', 'bg-lilac text-lilac-ink', 'bg-mint text-mint-ink', 'bg-blush text-blush-ink', 'bg-sand text-sand-ink'];
@endphp

<section {{ $attributes->class(['relative overflow-hidden py-20 font-display sm:py-28']) }}>
    <div class="guide-lines pointer-events-none absolute inset-0" aria-hidden="true"></div>

    <div class="container-page relative">
        <x-section-heading :eyebrow="$eyebrow" :title="$title" :lead="$lead" highlight="Deliver" :soft="true" />

        <div class="mt-14 grid gap-5 md:grid-cols-2 lg:grid-cols-3">
            @foreach (config('site.menu_services') as $block)
                @php
                    $isWide = in_array($block['title'], $wide, true);
                    $slug = collect($block['groups'])->pluck('slug')->filter()->first();
                    $url = $block['title'] === 'ERP as a Service' || ! $slug ? route('services.index') : route('services.show', $slug);
                @endphp

                <a href="{{ $url }}"
                   id="{{ \Illuminate\Support\Str::slug($block['title']) }}"
                   @class([
                       'reveal card card-hover group relative flex scroll-mt-28 flex-col overflow-hidden p-7 sm:p-8',
                       'md:col-span-2' => $isWide,
                   ])>

                    <div class="relative flex items-start justify-between gap-4">
                        <span class="leaf-diag flex h-12 w-12 shrink-0 items-center justify-center transition duration-300 group-hover:scale-105 {{ $tints[$loop->index % count($tints)] }}">
                            <x-icon :name="$block['icon']" class="h-6 w-6" />
                        </span>
                        <x-icon name="arrow-up-right" class="h-5 w-5 text-ink/25 transition group-hover:-translate-y-0.5 group-hover:translate-x-0.5 group-hover:text-brand-600" />
                    </div>

                    <h3 class="relative mt-6 text-xl font-semibold text-ink">{{ $block['title'] }}</h3>
                    <p class="relative mt-2 text-[15px] leading-relaxed">{{ $block['summary'] }}</p>

                    <div @class(['relative mt-6 grid gap-x-8 gap-y-5 border-t border-line pt-6', 'sm:grid-cols-2' => $isWide])>
                        @foreach ($block['groups'] as $group)
                            <div>
                                @if (! empty($group['label']))
                                    <p class="mb-2.5 text-[13px] font-semibold text-ink">{{ $group['label'] }}</p>
                                @endif
                                <ul class="flex flex-wrap gap-1.5">
                                    @foreach ($group['items'] as $item)
                                        <li class="rounded-full bg-surface px-3 py-1 text-[12.5px] leading-snug text-ink/75">{{ $item }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endforeach
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</section>
