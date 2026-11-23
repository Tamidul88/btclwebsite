@extends('layouts.app', ['transparentHeader' => true])

@section('title', 'Our Solutions — '.config('site.short_name'))
@section('description', 'Smart Delivery, Logistic Automation, Procure To Pay, SharePoint Solution and Salesforce Automation from Berger Tech Consulting Limited.')

@section('content')

    <x-hero
        eyebrow="Ready-made accelerators"
        title="Our Solutions"
        highlight="Solutions"
        subtitle="The right place for solution and innovation."
        cta="Get in Touch for Solution Demo"
        :cta-url="route('appointment')"
        image="images/solutions/innovation.jpg"
        :breadcrumbs="[['label' => 'Home', 'url' => route('home')], ['label' => 'Solutions']]"
        :eager="true"
    />

    <section class="bg-surface py-20 sm:py-24">
        <div class="container-page">
            <x-section-heading
                eyebrow="Ready-made accelerators"
                title="Solutions That Ship"
                lead="Configurable solutions for delivery, logistics, procurement, collaboration and sales — each built to remove a specific bottleneck."
            />

            <div class="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($solutions as $slug => $solution)
                    <a href="{{ route('solutions.show', $slug) }}"
                       class="reveal card card-hover group flex flex-col overflow-hidden">

                        <div class="h-48 w-full overflow-hidden">
                            <img src="{{ asset($solution['card_image']) }}" alt="{{ $solution['name'] }}"
                                 class="h-full w-full object-cover transition duration-500 group-hover:scale-105"
                                 loading="lazy" decoding="async">
                        </div>

                        <div class="flex grow flex-col p-6">
                            <h2 class="font-display text-lg font-bold text-ink transition group-hover:text-brand-600">{{ $solution['name'] }}</h2>
                            <p class="mt-3 grow text-sm leading-relaxed">{{ \Illuminate\Support\Str::limit($solution['card'], 140) }}</p>
                            <span class="mt-5 link-arrow">
                                Explore
                                <x-icon name="arrow" class="h-4 w-4 transition group-hover:translate-x-1" />
                            </span>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    <x-cta-band heading="Get in Touch for Solution Demo" />

@endsection
