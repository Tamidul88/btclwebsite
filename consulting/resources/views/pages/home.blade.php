@extends('layouts.app')

@section('title', config('site.company').' — '.config('site.tagline'))
@section('description', config('site.hero.lede'))

@section('content')

    <section class="border-b border-slate-200 bg-gradient-to-b from-brand-50 to-white">
        <div class="container-page py-20 lg:py-28">
            <div class="max-w-3xl">
                <p class="eyebrow">Independent technology consultancy</p>
                <h1 class="mt-4 text-4xl leading-[1.1] sm:text-5xl lg:text-6xl">{{ config('site.hero.title') }}</h1>
                <p class="mt-6 max-w-2xl text-lg leading-relaxed">{{ config('site.hero.lede') }}</p>
                <div class="mt-9 flex flex-wrap gap-3">
                    <a href="{{ route('consultation') }}" class="btn-primary">Request a consultation</a>
                    <a href="{{ route('case-studies') }}" class="btn-secondary">See our work</a>
                </div>
            </div>

            <dl class="mt-16 grid gap-6 border-t border-slate-200 pt-10 sm:grid-cols-4">
                @foreach (config('site.stats') as $stat)
                    <div>
                        <dt class="sr-only">{{ $stat['label'] }}</dt>
                        <dd>
                            <span class="block text-3xl font-semibold text-ink">{{ $stat['value'] }}</span>
                            <span class="mt-1 block text-sm text-slate-500">{{ $stat['label'] }}</span>
                        </dd>
                    </div>
                @endforeach
            </dl>
        </div>
    </section>

    <section class="container-page py-20">
        <p class="eyebrow">What we do</p>
        <h2 class="mt-3 text-3xl sm:text-4xl">Four practices, one senior team</h2>

        <div class="mt-12 grid gap-6 md:grid-cols-2">
            @foreach (config('site.services') as $slug => $service)
                <a href="{{ route('services.show', $slug) }}"
                   class="group rounded-xl border border-slate-200 p-7 transition hover:border-brand-600 hover:shadow-lg">
                    <h3 class="text-xl">{{ $service['name'] }}</h3>
                    <p class="mt-3 text-[15px] leading-relaxed">{{ $service['summary'] }}</p>
                    <span class="mt-5 inline-flex items-center gap-1.5 text-sm font-semibold text-brand-600">
                        Read more
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-4 w-4 transition group-hover:translate-x-1" aria-hidden="true">
                            <path d="M5 12h13M12.5 5.5 19 12l-6.5 6.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
                </a>
            @endforeach
        </div>
    </section>

    <section class="border-y border-slate-200 bg-slate-50">
        <div class="container-page py-20">
            <p class="eyebrow">How we work</p>
            <h2 class="mt-3 text-3xl sm:text-4xl">No surprises, by design</h2>

            <div class="mt-12 grid gap-8 sm:grid-cols-2 lg:grid-cols-4">
                @foreach (config('site.approach') as $i => $item)
                    <div>
                        <span class="text-sm font-semibold text-brand-600">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>
                        <h3 class="mt-2 text-lg">{{ $item['title'] }}</h3>
                        <p class="mt-2 text-sm leading-relaxed">{{ $item['body'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="container-page py-20">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <p class="eyebrow">Selected work</p>
                <h2 class="mt-3 text-3xl sm:text-4xl">Recent engagements</h2>
            </div>
            <a href="{{ route('case-studies') }}" class="btn-secondary">All case studies</a>
        </div>

        <div class="mt-12 grid gap-6 lg:grid-cols-3">
            @foreach (config('site.case_studies') as $slug => $study)
                <a href="{{ route('case-studies.show', $slug) }}"
                   class="group flex flex-col rounded-xl border border-slate-200 p-7 transition hover:border-brand-600 hover:shadow-lg">
                    <span class="eyebrow">{{ $study['sector'] }}</span>
                    <h3 class="mt-3 text-lg">{{ $study['name'] }}</h3>
                    <p class="mt-3 grow text-sm leading-relaxed">{{ $study['summary'] }}</p>
                    <span class="mt-5 text-sm font-semibold text-brand-600">Read the case study</span>
                </a>
            @endforeach
        </div>
    </section>

    <x-cta />

@endsection
