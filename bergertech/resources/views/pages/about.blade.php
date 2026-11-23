@extends('layouts.app', ['transparentHeader' => true])

@section('title', 'Our Story — '.config('site.short_name'))
@section('description', 'Berger Tech Consulting Limited, founded in 2022, is a next-generation technology solutions and services company and a subsidiary of Berger Paints Bangladesh Limited.')

@section('content')

    <x-hero
        eyebrow="Bergertech Story"
        title="Our Story"
        highlight="Story"
        subtitle="Empowering progress through technological advancements and digital transformation."
        cta="Set an Appointment"
        :cta-url="route('appointment')"
        :image="config('site.features.0.image')"
        :breadcrumbs="[['label' => 'Home', 'url' => route('home')], ['label' => 'Bergertech Story']]"
        :eager="true"
    />

    <section class="bg-white py-20 sm:py-24">
        <div class="container-page grid items-start gap-12 lg:grid-cols-[1.4fr_1fr] lg:gap-16">
            <div>
                <x-section-heading eyebrow="Who we are" title="Simplifying Business" align="left" />

                <div class="mt-8 space-y-5">
                    @foreach (config('site.story') as $index => $paragraph)
                        <p @class([
                            'leading-[1.85]',
                            'text-[17px] text-ink/80' => $index === 0,
                            'text-[15px]' => $index > 0,
                        ])>{{ $paragraph }}</p>
                    @endforeach
                </div>
            </div>

            <aside class="reveal overflow-hidden rounded-2xl shadow-float lg:sticky lg:top-28">
                <img src="{{ asset(config('site.story_image')) }}"
                     alt="The Berger Tech and Berger Paints IT teams at Tech Team Day by the Padma river"
                     class="aspect-4/3 w-full object-cover"
                     loading="lazy" decoding="async">

                <div class="bg-brand-panel relative p-8 text-white">
                <p class="font-display text-5xl font-black text-white">{{ config('site.founded') }}</p>
                <p class="mt-1 font-display text-xs tracking-[0.22em] text-white/70 uppercase">Founded</p>

                <dl class="mt-8 space-y-5 text-sm">
                    <div>
                        <dt class="font-display font-semibold text-white">Parent company</dt>
                        <dd class="mt-1 text-white/75">Berger Paints Bangladesh Limited</dd>
                    </div>
                    <div>
                        <dt class="font-display font-semibold text-white">Head office</dt>
                        <dd class="mt-1 text-white/75">{{ implode(', ', config('site.contact.address_lines')) }}</dd>
                    </div>
                    <div>
                        <dt class="font-display font-semibold text-white">Approach</dt>
                        <dd class="mt-1 text-white/75">Tech-agnostic, business outcome first</dd>
                    </div>
                </dl>
                </div>
            </aside>
        </div>
    </section>

    <section class="relative overflow-hidden bg-surface py-20 sm:py-28">
        <div class="grid-lines pointer-events-none absolute inset-0" aria-hidden="true"></div>

        <div class="container-page relative">
            <!-- <x-section-heading eyebrow="Why Berger Tech" title="Why We Are Premier Choice" /> -->

            <div class="mt-14 grid gap-4 sm:grid-cols-2 lg:grid-cols-4 lg:gap-5">
                @foreach (config('site.why_us') as $reason)
                    <div class="reveal card card-hover group flex flex-col gap-5 p-6">
                        <span class="flex items-start justify-between">
                            <span class="icon-tile h-14 w-14 rounded-xl transition group-hover:bg-brand-600 group-hover:text-white">
                                <x-mask-icon :src="$reason['icon']" class="h-8 w-8" />
                            </span>
                            <span class="font-display text-sm font-bold text-slate-300">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                        </span>
                        <p class="font-display text-[15px] leading-snug font-semibold text-ink">{{ $reason['text'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="relative overflow-hidden bg-surface py-20 sm:py-24">
        <img src="{{ asset(config('site.culture_image')) }}" alt=""
             class="absolute inset-0 h-full w-full object-cover opacity-10"
             loading="lazy" decoding="async" aria-hidden="true">
        <div class="container-page relative">
            <x-section-heading eyebrow="How we work" title="Our Culture" lead="Nurturing talent and building a thriving workplace." />

            <div class="mt-12 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                @foreach (config('site.culture') as $value)
                    <div class="reveal card card-hover p-7">
                        <span class="font-display text-sm font-bold text-brand-600">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                        <h3 class="mt-3 font-display text-lg font-bold text-ink">{{ $value['title'] }}</h3>
                        <p class="mt-4 text-sm leading-relaxed">{{ $value['text'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <x-cta-band heading="Work with us" text="Whether it is an ERP program or a single process, we start with a conversation." />

@endsection
