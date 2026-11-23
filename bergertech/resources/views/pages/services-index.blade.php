@extends('layouts.app', ['transparentHeader' => true])

@section('title', 'Our Core Services — '.config('site.short_name'))
@section('description', 'ERP, development, automation, security, audit, infrastructure and cloud services from Berger Tech Consulting Limited.')

@section('content')

    <x-hero
        eyebrow="What we do"
        title="Our Core Services"
        highlight="Services"
        subtitle="Value realization. Future readiness. Competitive advantage."
        cta="Set an Appointment"
        :cta-url="route('appointment')"
        image="images/services/sap-team.jpg"
        :breadcrumbs="[['label' => 'Home', 'url' => route('home')], ['label' => 'Services']]"
        :eager="true"
    />

    <section class="bg-white py-20 sm:py-24">
        <div class="container-page">
            <x-section-heading
                eyebrow="Platforms we partner on"
                title="Choose Your Platform"
                lead="Advisory, implementation, migration and support across the ERP and cloud platforms our customers depend on."
            />

            @inject('pageStyles', 'App\PageStyles')

            <div class="mt-12 grid gap-6 sm:grid-cols-2">
                @foreach ($services as $slug => $service)
                    <a href="{{ route('services.show', $slug) }}"
                       class="reveal card card-hover group flex flex-col overflow-hidden">

                        <div class="relative h-48 w-full overflow-hidden">
                            <img src="{{ asset($service['hero_image']) }}" alt="{{ $service['name'] }}"
                                 class="h-full w-full object-cover transition duration-500 group-hover:scale-105"
                                 loading="lazy" decoding="async">
                            <span class="absolute inset-x-0 bottom-0 h-1 {{ $pageStyles->classFor("background: {$service['accent']};") }}"></span>
                        </div>

                        <div class="flex grow flex-col p-7 sm:p-8">
                            <h2 class="font-display text-xl font-bold text-ink">{{ $service['name'] }}</h2>
                            <p class="mt-3 grow text-sm leading-relaxed">{{ $service['hero_subtitle'] }}</p>

                            <ul class="mt-5 space-y-2">
                                @foreach ($service['services'] as $item)
                                    <li class="flex gap-2.5 text-[13px] leading-snug">
                                        <x-icon name="check" class="mt-0.5 h-4 w-4 shrink-0 text-brand-600" />
                                        <span>{{ $item['title'] }}</span>
                                    </li>
                                @endforeach
                            </ul>

                            <span class="link-arrow mt-6 border-t border-line pt-5">
                                View service
                                <x-icon name="arrow" class="h-4 w-4 transition group-hover:translate-x-1" />
                            </span>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    {{-- The full "as a Service" catalogue, mirroring the site's mega menu. --}}
    <x-capabilities class="bg-surface" eyebrow="The full catalogue" />

    <x-tech-stack class="bg-white" />

    <x-cta-band heading="Not sure where to start?" text="Book a consultation and we will map the shortest route from where you are to where you want to be." />

@endsection
