@extends('layouts.app')
@section('title', 'Request a consultation — '.config('site.company'))
@section('description', 'Request a two-week fixed-fee discovery with '.config('site.company').'.')

@section('content')
    <section class="container-page py-16 lg:py-20">
        <div class="grid gap-14 lg:grid-cols-[1.2fr_1fr]">
            <div>
                <p class="eyebrow">Consultation</p>
                <h1 class="mt-3 text-4xl sm:text-5xl">Request a discovery</h1>
                <p class="mt-5 max-w-xl text-lg leading-relaxed">
                    Two weeks, fixed fee, senior engineers. You get a written assessment at the end that is yours to keep either way.
                </p>

                <div class="mt-10">
                    <x-enquiry-form type="consultation" :detailed="true" />
                </div>
            </div>

            <aside class="lg:pt-24">
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-7">
                    <h2 class="text-lg">What happens next</h2>
                    <ol class="mt-4 space-y-4 text-sm">
                        <li><span class="font-semibold text-ink">1.</span> We reply within one working day to arrange a 30-minute call.</li>
                        <li><span class="font-semibold text-ink">2.</span> We scope the discovery together and agree a fixed fee.</li>
                        <li><span class="font-semibold text-ink">3.</span> Two weeks later you have a written assessment and a ranked backlog.</li>
                    </ol>

                    <h2 class="mt-7 text-lg">Common questions</h2>
                    <dl class="mt-4 space-y-4 text-sm">
                        @foreach (config('site.faqs') as $faq)
                            <div>
                                <dt class="font-medium text-ink">{{ $faq['q'] }}</dt>
                                <dd class="mt-1 leading-relaxed">{{ $faq['a'] }}</dd>
                            </div>
                        @endforeach
                    </dl>
                </div>
            </aside>
        </div>
    </section>
@endsection
