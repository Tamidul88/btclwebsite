@extends('layouts.app', ['transparentHeader' => true])

@section('title', $solution['name'].' — '.config('site.short_name'))
@section('description', \Illuminate\Support\Str::limit($solution['card'], 155))

@section('content')

    <x-hero
        eyebrow="Solution"
        :title="$solution['name']"
        :subtitle="$solution['card']"
        cta="Get in Touch for Solution Demo"
        :cta-url="route('appointment')"
        :image="$solution['hero_image']"
        :accent="$solution['hero_gradient'][1]"
        :breadcrumbs="[['label' => 'Home', 'url' => route('home')], ['label' => 'Solutions', 'url' => route('solutions.index')], ['label' => $solution['name']]]"
        :eager="true"
    />

    <section class="bg-white py-20 sm:py-24">
        <div class="container-page max-w-3xl text-center">
            <p class="eyebrow justify-center">Overview</p>
            <p class="mt-6 font-display text-xl leading-[1.7] text-ink/85 sm:text-2xl sm:leading-[1.6]">{{ $solution['summary'] }}</p>
        </div>
    </section>

    @foreach ($solution['sections'] as $index => $section)
        <section class="{{ $index % 2 === 0 ? 'bg-surface' : 'bg-white' }} py-20 sm:py-24">
            <div class="container-page">
                <x-section-heading :title="$section['title']" />

                @if ($section['style'] === 'grid')
                    <div class="mt-12 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach ($section['items'] as $item)
                            <div class="reveal card card-hover flex gap-4 p-5">
                                <span class="icon-tile h-9 w-9"><x-icon name="check" class="h-5 w-5" /></span>
                                <p class="self-center text-sm leading-snug text-ink/85">{{ $item }}</p>
                            </div>
                        @endforeach
                    </div>

                @elseif ($section['style'] === 'steps')
                    <ol class="mt-12 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                        @foreach ($section['items'] as $i => $item)
                            <li class="reveal card relative overflow-hidden p-6">
                                <span class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-brand-600 to-brand-300"></span>
                                <span class="font-display text-3xl font-black text-brand-200">
                                    {{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}
                                </span>
                                <p class="mt-2 text-sm leading-snug text-ink/85">{{ $item }}</p>
                            </li>
                        @endforeach
                    </ol>

                @else
                    <ul class="card mx-auto mt-12 grid max-w-3xl gap-x-8 gap-y-4 p-8 sm:grid-cols-2">
                        @foreach ($section['items'] as $item)
                            <li class="flex gap-3 text-[15px] leading-snug">
                                <x-icon name="check" class="mt-0.5 h-4 w-4 shrink-0 text-brand-600" />
                                <span>{{ $item }}</span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </section>
    @endforeach

    {{-- Business benefits --}}
    <section class="relative overflow-hidden bg-white py-20 sm:py-28">
        <div class="bg-mesh pointer-events-none absolute inset-0" aria-hidden="true"></div>

        <div class="container-page relative">
            <x-section-heading eyebrow="The outcome" title="Business Benefits" highlight="Benefits" />

            @inject('pageStyles', 'App\PageStyles')
            @php
                [$gradientFrom, $gradientTo] = $solution['hero_gradient'];
                $benefitBadgeClass = $pageStyles->classFor("background: linear-gradient(135deg, {$gradientFrom}, {$gradientTo});");
            @endphp

            <div class="mt-14 grid gap-5 md:grid-cols-3">
                @foreach ($solution['benefits'] as $benefit)
                    <div class="reveal card card-hover p-7">
                        <span class="flex h-11 w-11 items-center justify-center rounded-xl font-display text-sm font-extrabold text-white shadow-[0_10px_24px_-10px_rgb(31_39_80/0.6)] {{ $benefitBadgeClass }}">
                            {{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}
                        </span>
                        <p class="mt-5 text-[16px] leading-relaxed text-ink/80">{{ $benefit }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Other solutions --}}
    <section class="bg-surface py-20">
        <div class="container-page">
            <div class="flex items-end justify-between gap-4">
                <h2 class="font-display text-2xl font-bold text-ink">Other solutions</h2>
                <a href="{{ route('solutions.index') }}" class="link-arrow">View all <x-icon name="arrow" class="h-4 w-4" /></a>
            </div>

            <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                @foreach (collect(config('solutions'))->except($slug) as $otherSlug => $other)
                    <a href="{{ route('solutions.show', $otherSlug) }}"
                       class="card card-hover group overflow-hidden">
                        <span class="block h-28 overflow-hidden">
                            <img src="{{ asset($other['card_image']) }}" alt="{{ $other['name'] }}"
                                 class="h-full w-full object-cover transition duration-500 group-hover:scale-105" loading="lazy" decoding="async">
                        </span>
                        <span class="block p-5">
                            <span class="font-display text-sm font-bold text-ink group-hover:text-brand-600">{{ $other['name'] }}</span>
                            <span class="mt-2 block text-[13px] leading-snug">{{ \Illuminate\Support\Str::limit($other['card'], 70) }}</span>
                        </span>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    <x-cta-band heading="Get in Touch for Solution Demo" />

@endsection
