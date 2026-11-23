@extends('layouts.app')
@section('title', 'About — '.config('site.company'))
@section('description', 'An independent consultancy of senior engineers, operating since '.config('site.founded').'.')

@section('content')
    <section class="border-b border-slate-200 bg-brand-50">
        <div class="container-page py-16 lg:py-20">
            <p class="eyebrow">About</p>
            <h1 class="mt-3 max-w-3xl text-4xl sm:text-5xl">A small firm, on purpose</h1>
            <p class="mt-5 max-w-2xl text-lg leading-relaxed">
                {{ config('site.company') }} has operated since {{ config('site.founded') }}. We stay small because the work we are good at does not scale by adding juniors to it.
            </p>
        </div>
    </section>

    <section class="container-page py-16">
        <div class="grid gap-12 lg:grid-cols-2">
            <div class="space-y-5">
                <h2 class="text-2xl">How we got here</h2>
                <p class="text-[15px] leading-[1.9]">We started after years inside organisations where the same pattern kept repeating: a platform built quickly to find product-market fit, then asked to carry ten times the load and twenty times the team, with nobody given time to change its shape.</p>
                <p class="text-[15px] leading-[1.9]">That work is unglamorous and it is rarely urgent until it is an outage. We built a firm that does only that, with people senior enough to make the judgement calls it requires.</p>
                <p class="text-[15px] leading-[1.9]">We take on a small number of engagements at a time. If we are not the right people for a piece of work, we will say so on the first call.</p>
            </div>

            <div>
                <h2 class="text-2xl">The team</h2>
                <ul class="mt-6 divide-y divide-slate-200 border-y border-slate-200">
                    @foreach (config('site.team') as $member)
                        <li class="py-4">
                            <p class="font-medium text-ink">{{ $member['name'] }}</p>
                            <p class="mt-0.5 text-sm text-brand-700">{{ $member['role'] }}</p>
                            <p class="mt-1 text-sm">{{ $member['focus'] }}</p>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </section>

    <x-cta />
@endsection
