@extends('layouts.app', ['transparentHeader' => true])

@section('title', 'Resource Center — '.config('site.short_name'))
@section('description', 'Insights, the full service catalogue, solution briefs, our technology stack and answers to common questions from Berger Tech Consulting Limited.')

@section('content')

    <x-hero
        eyebrow="Resources"
        title="Resource Center"
        highlight="Center"
        subtitle="Insights, service guides, solution briefs and answers — everything you need to plan your next step with Berger Tech."
        cta="Book a Consultation"
        :cta-url="route('appointment')"
        secondary-cta="Browse Guides"
        secondary-url="#guides"
        image="images/features/roadmap.webp"
        :breadcrumbs="[['label' => 'Home', 'url' => route('home')], ['label' => 'Resources']]"
        :eager="true"
    />

    {{-- ============================ QUICK LINKS ============================= --}}
    <section id="guides" class="relative overflow-hidden bg-white py-20 sm:py-28">
        <div class="bg-mesh pointer-events-none absolute inset-0" aria-hidden="true"></div>

        <div class="container-page relative">
            <x-section-heading
                eyebrow="Start here"
                title="Find what you need"
                highlight="you need"
                lead="Jump straight to the resource that answers your question."
            />

            <div class="mt-14 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ([
                    ['icon' => 'book', 'title' => 'Insights & guides', 'text' => 'Practical guides on transformation, SAP-to-cloud migration, ERP choice and automation.', 'url' => '#insights'],
                    ['icon' => 'layers', 'title' => 'Service catalogue', 'text' => 'All seven service lines and everything included in each one.', 'url' => '#catalogue'],
                    ['icon' => 'file', 'title' => 'Solution briefs', 'text' => 'One-page overviews of our ready-made business solutions.', 'url' => '#briefs'],
                    ['icon' => 'cpu', 'title' => 'Technology stack', 'text' => 'The platforms we implement, integrate and support.', 'url' => '#stack'],
                    ['icon' => 'help', 'title' => 'FAQ', 'text' => 'Quick answers about services, support, cloud and booking.', 'url' => '#faq'],
                    ['icon' => 'calendar', 'title' => 'Book a consultation', 'text' => 'A 30-minute call with a consultant, online or at Berger House.', 'url' => route('appointment')],
                ] as $link)
                    <a href="{{ $link['url'] }}" class="reveal card card-hover group flex items-start gap-5 p-6 sm:p-7">
                        <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-brand-600 to-brand-400 text-white shadow-[0_10px_24px_-10px_rgb(31_39_80/0.7)]">
                            <x-icon :name="$link['icon']" class="h-6 w-6" />
                        </span>
                        <span class="min-w-0">
                            <span class="flex items-center gap-2 font-display text-lg font-bold text-ink">
                                {{ $link['title'] }}
                                <x-icon name="arrow" class="h-4 w-4 text-brand-600 opacity-0 transition group-hover:translate-x-1 group-hover:opacity-100" />
                            </span>
                            <span class="mt-1.5 block text-[14px] leading-relaxed">{{ $link['text'] }}</span>
                        </span>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ============================== GUIDES ================================ --}}
    <section id="insights" class="scroll-mt-20 bg-surface py-20 sm:py-28">
        <div class="container-page">
            <div class="flex flex-col gap-6 md:flex-row md:items-end md:justify-between">
                <x-section-heading
                    eyebrow="Insights & guides"
                    title="Read before you decide"
                    highlight="before you decide"
                    lead="Practical guides for business and technology leaders planning their next step."
                    align="left"
                />
                <a href="{{ route('insights.index') }}" class="link-arrow shrink-0">
                    All insights
                    <x-icon name="arrow" class="h-4 w-4" />
                </a>
            </div>

            <div class="mt-12 grid gap-6 md:grid-cols-2">
                @foreach (app(\App\InsightLibrary::class)->all() as $insight)
                    <x-insight-card :insight="$insight" />
                @endforeach
            </div>
        </div>
    </section>

    <x-capabilities id="catalogue" class="scroll-mt-20 bg-white" eyebrow="Service catalogue" />

    {{-- =========================== SOLUTION BRIEFS ========================== --}}
    <section id="briefs" class="bg-surface py-20 sm:py-28">
        <div class="container-page">
            <x-section-heading
                eyebrow="Solution briefs"
                title="Our solutions at a glance"
                highlight="at a glance"
                lead="What each solution does and the problem it solves. Open a brief for the full feature list and business benefits."
            />

            <div class="mt-14 space-y-4">
                @foreach ($solutions as $slug => $solution)
                    <a href="{{ route('solutions.show', $slug) }}"
                       class="reveal card card-hover group grid items-center gap-6 overflow-hidden p-4 sm:grid-cols-[180px_1fr_auto] sm:p-5">
                        <span class="block h-40 overflow-hidden rounded-xl sm:h-28">
                            <img src="{{ asset($solution['card_image']) }}" alt="{{ $solution['name'] }}"
                                 class="h-full w-full object-cover transition duration-500 group-hover:scale-105"
                                 loading="lazy" decoding="async">
                        </span>
                        <span class="block px-2 sm:px-0">
                            <span class="block font-display text-lg font-bold text-ink">{{ $solution['name'] }}</span>
                            <span class="mt-1.5 block text-[14px] leading-relaxed">{{ \Illuminate\Support\Str::limit($solution['summary'] ?? $solution['card'], 190) }}</span>
                        </span>
                        <span class="link-arrow px-2 pb-2 sm:px-4 sm:pb-0">
                            Read brief
                            <x-icon name="arrow" class="h-4 w-4 transition group-hover:translate-x-1" />
                        </span>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    <x-tech-stack id="stack" class="scroll-mt-20 bg-white" />

    <x-faq class="bg-surface" />

    <x-cta-band heading="Can't find what you need?" text="Tell us what you are working on and a consultant will point you to the right resource — or the right person." />

@endsection
