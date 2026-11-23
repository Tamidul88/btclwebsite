@extends('layouts.app', ['transparentHeader' => true])

@section('title', 'Insights — '.config('site.short_name'))
@section('description', 'Practical guides on digital transformation, SAP and cloud migration, choosing an ERP and business process automation from Berger Tech Consulting Limited.')

@section('content')

    <x-hero
        eyebrow="Insights"
        title="Insights for business leaders"
        highlight="business leaders"
        subtitle="Practical guides on digital transformation, ERP, cloud and automation — what to do first, and what to avoid."
        cta="Book a Consultation"
        :cta-url="route('appointment')"
        secondary-cta="Resource Center"
        :secondary-url="route('resources')"
        :image="$insights->first()['image']"
        :breadcrumbs="[['label' => 'Home', 'url' => route('home')], ['label' => 'Insights']]"
        :eager="true"
    />

    <section class="relative overflow-hidden bg-white py-20 sm:py-28">
        <div class="bg-mesh pointer-events-none absolute inset-0" aria-hidden="true"></div>

        <div class="container-page relative">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <h2 class="font-display text-2xl font-bold text-ink">Featured guide</h2>
                <ul class="flex flex-wrap gap-2" aria-label="Topics">
                    @foreach ($insights->pluck('category')->unique() as $category)
                        <li class="rounded-full border border-line bg-white px-3 py-1 text-[12.5px] font-medium text-ink/75">{{ $category }}</li>
                    @endforeach
                </ul>
            </div>

            <x-insight-card :insight="$insights->first()" featured class="mt-8" />

            <h2 class="mt-20 font-display text-2xl font-bold text-ink">All guides</h2>

            <div class="mt-8 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                @foreach ($insights->skip(1) as $insight)
                    <x-insight-card :insight="$insight" />
                @endforeach
            </div>
        </div>
    </section>

    <x-cta-band
        heading="Want this applied to your business?"
        text="Book a consultation and we will walk through what these ideas mean for your systems, your processes and your goals."
    />

@endsection
