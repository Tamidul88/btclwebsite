@extends('layouts.app')
@section('title', $service['name'].' — '.config('site.company'))
@section('description', $service['summary'])

@section('content')
    <section class="border-b border-slate-200 bg-brand-50">
        <div class="container-page py-16 lg:py-20">
            <nav class="text-sm" aria-label="Breadcrumb">
                <a href="{{ route('services') }}" class="text-brand-700 hover:underline">Services</a>
                <span class="mx-2 text-slate-400" aria-hidden="true">/</span>
                <span class="text-slate-500">{{ $service['name'] }}</span>
            </nav>
            <h1 class="mt-4 max-w-3xl text-4xl sm:text-5xl">{{ $service['name'] }}</h1>
            <p class="mt-5 max-w-2xl text-lg leading-relaxed">{{ $service['summary'] }}</p>
        </div>
    </section>

    <section class="container-page py-16">
        <div class="grid gap-12 lg:grid-cols-[1.4fr_1fr]">
            <div class="space-y-5">
                @foreach ($service['body'] as $paragraph)
                    <p class="text-[15px] leading-[1.9]">{{ $paragraph }}</p>
                @endforeach
            </div>

            <aside>
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-7">
                    <h2 class="text-lg">What you get</h2>
                    <ul class="mt-4 space-y-3">
                        @foreach ($service['deliverables'] as $item)
                            <li class="flex gap-2.5 text-sm leading-snug">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="mt-0.5 h-4 w-4 shrink-0 text-brand-600" aria-hidden="true">
                                    <path d="m4 12.5 5 5L20 6.5" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                <span>{{ $item }}</span>
                            </li>
                        @endforeach
                    </ul>
                    <a href="{{ route('consultation') }}" class="btn-primary mt-6 w-full">Discuss this work</a>
                </div>
            </aside>
        </div>
    </section>

    <x-cta />
@endsection
