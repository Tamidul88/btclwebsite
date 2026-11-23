@extends('layouts.app', ['transparentHeader' => true])

@section('title', $service['name'].' — '.config('site.short_name'))
@section('description', $service['hero_subtitle'])

@section('content')

    @inject('pageStyles', 'App\PageStyles')

    <x-hero
        :eyebrow="$service['name']"
        :title="$service['hero_title']"
        :subtitle="$service['hero_subtitle']"
        cta="Learn More"
        cta-url="#detail"
        secondary-cta="Set an Appointment"
        :secondary-url="route('appointment')"
        :image="$service['hero_image']"
        :accent="$service['accent']"
        :breadcrumbs="[['label' => 'Home', 'url' => route('home')], ['label' => 'Services', 'url' => route('services.index')], ['label' => $service['name']]]"
        :eager="true"
    />

    {{-- Intro --}}
    <section id="detail" class="bg-white py-20 sm:py-24">
        <div class="container-page">
            <x-section-heading :eyebrow="$service['name']" :title="$service['intro_title']" />

            <div class="mt-12 grid items-start gap-12 lg:grid-cols-[1.25fr_1fr]">
                <div class="space-y-5">
                    @foreach ($service['intro'] as $paragraph)
                        <p class="text-[15px] leading-[1.9]">{{ $paragraph }}</p>
                    @endforeach
                </div>

                <div class="reveal overflow-hidden rounded-2xl bg-surface shadow-float">
                    <img src="{{ asset($service['photo']) }}" alt="{{ $service['name'] }}"
                         class="w-full object-contain p-4 lg:aspect-4/3 lg:object-cover lg:p-0"
                         loading="lazy" decoding="async">
                </div>
            </div>
        </div>
    </section>

    {{-- Service cards --}}
    <section class="bg-surface py-20 sm:py-24">
        <div class="container-page">
            <x-section-heading eyebrow="What we offer" title="Our Service" />

            <div class="mt-12 grid gap-6 sm:grid-cols-2">
                @foreach ($service['services'] as $item)
                    <article class="reveal card card-hover flex flex-col p-7 sm:p-8">
                        <span class="flex items-center gap-3">
                            <span class="flex h-10 w-10 items-center justify-center rounded-lg font-display text-sm font-bold text-white {{ $pageStyles->classFor("background: {$service['accent']};") }}">
                                {{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}
                            </span>
                            <h3 class="font-display text-lg font-bold text-ink">{{ $item['title'] }}</h3>
                        </span>
                        <p class="mt-3 text-sm leading-relaxed">{{ $item['text'] }}</p>

                        <details class="group mt-auto pt-5">
                            <summary class="link-arrow cursor-pointer list-none [&::-webkit-details-marker]:hidden">
                                Learn more
                                <x-icon name="arrow" class="h-4 w-4 transition group-open:rotate-90" />
                            </summary>
                            <ul class="mt-4 space-y-2.5 border-t border-line pt-4">
                                @foreach ($item['details'] as $detail)
                                    <li class="flex gap-2.5 text-sm leading-snug">
                                        <x-icon name="check" class="mt-0.5 h-4 w-4 shrink-0 text-brand-600" />
                                        <span>{{ $detail }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </details>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Why us --}}
    {{-- overflow-clip, not hidden: a scroll container would break the sticky heading. --}}
    <section class="relative overflow-clip bg-white py-20 sm:py-28">
        <div class="bg-mesh pointer-events-none absolute inset-0" aria-hidden="true"></div>

        <div class="container-page relative grid gap-12 lg:grid-cols-[1fr_1.4fr] lg:gap-16">
            <x-section-heading
                eyebrow="Our difference"
                title="Why Berger Tech?"
                highlight="Berger Tech?"
                lead="What you can expect when you work with our {{ $service['name'] }} team."
                align="left"
                class="lg:sticky lg:top-28 lg:self-start"
            />

            <div class="space-y-4">
                @foreach ($service['why'] as $reason)
                    <div class="reveal card card-hover flex gap-5 p-6">
                        <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl text-white shadow-[0_10px_24px_-10px_rgb(31_39_80/0.6)] {{ $pageStyles->classFor("background: linear-gradient(135deg, #1f2750, {$service['accent']});") }}">
                            <x-icon name="check" class="h-5 w-5" />
                        </span>
                        <p class="self-center text-[16px] leading-relaxed text-ink/80">{{ $reason }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Other services --}}
    @php
        $logos = collect(config('site.service_cards'))->pluck('logo', 'slug');
    @endphp
    <section class="bg-surface py-20">
        <div class="container-page">
            <div class="flex items-end justify-between gap-4">
                <h2 class="font-display text-2xl font-bold text-ink">Explore other services</h2>
                <a href="{{ route('services.index') }}" class="link-arrow shrink-0">
                    All services
                    <x-icon name="arrow" class="h-4 w-4" />
                </a>
            </div>

            <div class="mt-8 grid gap-4 sm:grid-cols-3">
                @foreach (collect(config('services_content'))->except($slug) as $otherSlug => $other)
                    <a href="{{ route('services.show', $otherSlug) }}" class="card card-hover group flex items-center gap-4 p-5">
                        <span class="flex h-14 w-20 shrink-0 items-center justify-center rounded-xl bg-surface px-2 ring-1 ring-line">
                            <img src="{{ asset($logos[$otherSlug] ?? config('site.logo')) }}" alt="" class="max-h-8 w-auto max-w-full object-contain" loading="lazy" decoding="async">
                        </span>
                        <span class="min-w-0">
                            <span class="block font-display text-[16px] font-bold text-ink transition group-hover:text-brand-600">{{ $other['name'] }}</span>
                            <span class="mt-0.5 line-clamp-2 block text-[13px] leading-snug">{{ $other['hero_subtitle'] }}</span>
                        </span>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    <x-cta-band
        heading="Talk to our {{ $service['name'] }} team"
        text="Book a consultation and we will walk through your landscape, the gaps, and what a realistic roadmap looks like."
    />

@endsection
