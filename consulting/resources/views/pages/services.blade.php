@extends('layouts.app')
@section('title', 'Services — '.config('site.company'))
@section('description', 'Platform modernisation, data engineering, security engineering and cloud reliability.')

@section('content')
    <section class="border-b border-slate-200 bg-brand-50">
        <div class="container-page py-16 lg:py-20">
            <p class="eyebrow">Services</p>
            <h1 class="mt-3 max-w-3xl text-4xl sm:text-5xl">Four practices, delivered by people who have done it before</h1>
            <p class="mt-5 max-w-2xl text-lg leading-relaxed">Each engagement starts with a short discovery and is sequenced so any single step can be reversed inside a day.</p>
        </div>
    </section>

    <section class="container-page py-16">
        <div class="grid gap-6 md:grid-cols-2">
            @foreach (config('site.services') as $slug => $service)
                <a href="{{ route('services.show', $slug) }}" class="group flex flex-col rounded-xl border border-slate-200 p-7 transition hover:border-brand-600 hover:shadow-lg">
                    <h2 class="text-xl">{{ $service['name'] }}</h2>
                    <p class="mt-3 grow text-[15px] leading-relaxed">{{ $service['summary'] }}</p>
                    <ul class="mt-5 space-y-1.5">
                        @foreach (array_slice($service['deliverables'], 0, 3) as $item)
                            <li class="text-sm text-slate-500">— {{ $item }}</li>
                        @endforeach
                    </ul>
                    <span class="mt-5 text-sm font-semibold text-brand-600">Read more</span>
                </a>
            @endforeach
        </div>
    </section>

    <x-cta />
@endsection
