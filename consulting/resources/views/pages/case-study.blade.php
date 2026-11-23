@extends('layouts.app')
@section('title', $study['name'].' — '.config('site.company'))
@section('description', $study['summary'])
@section('og_type', 'article')

@section('content')
    <section class="border-b border-slate-200 bg-brand-50">
        <div class="container-page py-16 lg:py-20">
            <nav class="text-sm" aria-label="Breadcrumb">
                <a href="{{ route('case-studies') }}" class="text-brand-700 hover:underline">Case studies</a>
                <span class="mx-2 text-slate-400" aria-hidden="true">/</span>
                <span class="text-slate-500">{{ $study['sector'] }}</span>
            </nav>
            <h1 class="mt-4 max-w-3xl text-4xl sm:text-5xl">{{ $study['name'] }}</h1>
            <p class="mt-5 max-w-2xl text-lg leading-relaxed">{{ $study['summary'] }}</p>
        </div>
    </section>

    <section class="container-page py-16">
        <dl class="grid gap-6 border-b border-slate-200 pb-10 sm:grid-cols-3">
            @foreach ($study['metrics'] as $metric)
                <div>
                    <dd class="text-3xl font-semibold text-ink">{{ $metric['value'] }}</dd>
                    <dt class="mt-1 text-sm text-slate-500">{{ $metric['label'] }}</dt>
                </div>
            @endforeach
        </dl>

        <div class="mt-12 grid gap-10 lg:grid-cols-3">
            <div>
                <h2 class="text-lg">The challenge</h2>
                <p class="mt-3 text-[15px] leading-[1.9]">{{ $study['challenge'] }}</p>
            </div>
            <div>
                <h2 class="text-lg">Our approach</h2>
                <p class="mt-3 text-[15px] leading-[1.9]">{{ $study['approach'] }}</p>
            </div>
            <div>
                <h2 class="text-lg">The outcome</h2>
                <p class="mt-3 text-[15px] leading-[1.9]">{{ $study['outcome'] }}</p>
            </div>
        </div>
    </section>

    <x-cta />
@endsection
