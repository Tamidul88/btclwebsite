@extends('layouts.app', ['transparentHeader' => true])

@section('title', $insight['title'].' — '.config('site.short_name'))
@section('description', $insight['excerpt'])

@section('content')

    <x-hero
        :eyebrow="$insight['category']"
        :title="$insight['title']"
        :subtitle="$insight['excerpt']"
        :image="$insight['image']"
        :breadcrumbs="[['label' => 'Home', 'url' => route('home')], ['label' => 'Insights', 'url' => route('insights.index')], ['label' => $insight['title']]]"
        :eager="true"
    >
        <p class="flex flex-wrap items-center gap-x-6 gap-y-2 text-[13px] text-white/60">
            <span class="flex items-center gap-2">
                <x-icon name="clock" class="h-4 w-4" />
                {{ $insight['read_minutes'] }} min read
            </span>
            <span class="flex items-center gap-2">
                <x-icon name="book" class="h-4 w-4" />
                {{ count($insight['sections']) }} sections
            </span>
        </p>
    </x-hero>

    <section class="bg-white py-16 sm:py-24">
        <div class="container-page grid gap-12 lg:grid-cols-[minmax(0,1fr)_300px] lg:gap-16">

            <article class="min-w-0 max-w-[720px]">
                <div class="rounded-2xl border border-brand-100 bg-gradient-to-br from-brand-50 to-white p-6 sm:p-8">
                    <p class="flex items-center gap-3 font-display text-[15px] font-bold text-ink">
                        <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-br from-brand-600 to-brand-400 text-white">
                            <x-icon name="sparkle" class="h-4 w-4" />
                        </span>
                        Key takeaways
                    </p>
                    <ul class="mt-5 space-y-3">
                        @foreach ($insight['takeaways'] as $takeaway)
                            <li class="flex gap-3 text-[15px] leading-relaxed text-ink/80">
                                <x-icon name="check" class="mt-1 h-4 w-4 shrink-0 text-brand-600" />
                                {{ $takeaway }}
                            </li>
                        @endforeach
                    </ul>
                </div>

                @foreach ($insight['sections'] as $section)
                    <section id="{{ \Illuminate\Support\Str::slug($section['heading']) }}" class="scroll-mt-28" data-toc-section>
                        <h2 class="mt-14 font-display text-[26px] leading-tight font-bold tracking-[-0.02em] text-ink sm:text-[28px]">{{ $section['heading'] }}</h2>

                        @foreach ($section['paragraphs'] as $paragraph)
                            <p class="mt-5 text-[17px] leading-[1.8] text-ink/75">{{ $paragraph }}</p>
                        @endforeach

                        @if (! empty($section['points']))
                            <ul class="mt-6 space-y-3">
                                @foreach ($section['points'] as $point)
                                    <li class="flex gap-3.5 text-base leading-relaxed text-ink/75">
                                        <span class="mt-2.5 h-2 w-2 shrink-0 rounded-full bg-gradient-to-br from-brand-600 to-[#7c5cff]"></span>
                                        <span>{{ $point }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </section>
                @endforeach

                <div class="relative mt-16 overflow-hidden rounded-2xl bg-brand-950 p-8 text-white sm:p-10">
                    <div class="grid-lines-dark pointer-events-none absolute inset-0" aria-hidden="true"></div>
                    <div class="pointer-events-none absolute -top-20 -right-16 h-60 w-60 rounded-full bg-brand-500/40 blur-3xl" aria-hidden="true"></div>

                    <div class="relative flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <p class="font-display text-xl font-bold text-white">Ready to put this into practice?</p>
                            <p class="mt-2 text-[15px] text-white/70">A {{ config('site.booking.duration') }}-minute consultation, online or at Berger House.</p>
                        </div>
                        <a href="{{ route('appointment') }}" class="btn-white shrink-0">
                            <x-icon name="calendar" class="h-4 w-4" />
                            Book a consultation
                        </a>
                    </div>
                </div>
            </article>

            <aside class="hidden lg:block">
                <div class="sticky top-28 space-y-6">
                    <nav aria-label="On this page" class="card p-6">
                        <p class="text-[11px] font-bold tracking-[0.16em] text-muted uppercase">On this page</p>
                        <ol class="mt-4 space-y-1 border-l border-line">
                            @foreach ($insight['sections'] as $section)
                                <li>
                                    <a href="#{{ \Illuminate\Support\Str::slug($section['heading']) }}"
                                       data-toc-link
                                       class="-ml-px block border-l-2 border-transparent py-1 pl-4 text-[14px] leading-snug text-muted transition hover:border-brand-300 hover:text-ink data-[active]:border-brand-600 data-[active]:font-semibold data-[active]:text-ink">
                                        {{ $section['heading'] }}
                                    </a>
                                </li>
                            @endforeach
                        </ol>
                    </nav>

                    <div class="card p-6">
                        <p class="font-display text-[15px] font-bold text-ink">Talk to a consultant</p>
                        <p class="mt-2 text-[14px] leading-relaxed">Get advice on your own systems and roadmap.</p>
                        <a href="{{ route('contact') }}" class="link-arrow mt-4">
                            Contact us
                            <x-icon name="arrow" class="h-4 w-4" />
                        </a>
                    </div>
                </div>
            </aside>
        </div>
    </section>

    <section class="bg-surface py-20 sm:py-24">
        <div class="container-page">
            <div class="flex flex-col gap-6 md:flex-row md:items-end md:justify-between">
                <x-section-heading eyebrow="Keep reading" title="More insights" highlight="insights" align="left" />
                <a href="{{ route('insights.index') }}" class="link-arrow shrink-0">
                    All insights
                    <x-icon name="arrow" class="h-4 w-4" />
                </a>
            </div>

            <div class="mt-12 grid gap-6 md:grid-cols-3">
                @foreach ($related as $item)
                    <x-insight-card :insight="$item" />
                @endforeach
            </div>
        </div>
    </section>

@endsection
