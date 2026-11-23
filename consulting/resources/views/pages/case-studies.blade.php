@extends('layouts.app')
@section('title', 'Case studies — '.config('site.company'))
@section('description', 'Selected engagements and what changed as a result.')

@section('content')
    <section class="border-b border-slate-200 bg-brand-50">
        <div class="container-page py-16 lg:py-20">
            <p class="eyebrow">Case studies</p>
            <h1 class="mt-3 max-w-3xl text-4xl sm:text-5xl">What changed, and by how much</h1>
        </div>
    </section>

    <section class="container-page py-16">
        <div class="grid gap-6 lg:grid-cols-3">
            @foreach (config('site.case_studies') as $slug => $study)
                <a href="{{ route('case-studies.show', $slug) }}" class="flex flex-col rounded-xl border border-slate-200 p-7 transition hover:border-brand-600 hover:shadow-lg">
                    <span class="eyebrow">{{ $study['sector'] }}</span>
                    <h2 class="mt-3 text-lg">{{ $study['name'] }}</h2>
                    <p class="mt-3 grow text-sm leading-relaxed">{{ $study['summary'] }}</p>
                    <span class="mt-5 text-sm font-semibold text-brand-600">Read the case study</span>
                </a>
            @endforeach
        </div>
    </section>

    <x-cta />
@endsection
