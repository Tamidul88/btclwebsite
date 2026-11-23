@extends('layouts.app', ['transparentHeader' => true, 'coverHeader' => true])

@section('title', 'Berger Tech Consulting Ltd. — Simplifying Business')
@section('description', 'Berger Tech Consulting Limited simplifies business through SAP, Microsoft Dynamics 365, cloud, automation and security services.')

@section('content')

    <x-hero-cover
        :title="config('site.hero.title')"
        :lines="config('site.hero.lines')"
        :cta="config('site.hero.cta')"
        :cta-url="route('appointment')"
        :image="config('site.hero.image')"
        image-position="60% center"
    />

    {{-- ============================ OUR SERVICES ============================ --}}
    <section id="services" class="relative overflow-hidden bg-white py-20 sm:py-28">
        <div class="bg-mesh pointer-events-none absolute inset-0" aria-hidden="true"></div>

        <div class="container-page relative">
            <x-section-heading
                eyebrow="What we do"
                title="Our Services"
                lead="Certified partners for the platforms that run modern enterprises — from ERP to the cloud it lives on."
            />

            <div class="mt-14 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                @foreach (config('site.service_cards') as $card)
                    <a href="{{ route('services.show', $card['slug']) }}"
                       class="reveal card card-hover group relative flex flex-col overflow-hidden p-7">
                        <span class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-brand-600 via-brand-400 to-[#7c5cff] opacity-0 transition group-hover:opacity-100" aria-hidden="true"></span>

                        <span class="flex h-16 items-center justify-center rounded-xl bg-surface px-4 ring-1 ring-line">
                            <img src="{{ asset($card['logo']) }}" alt="{{ $card['badge'] }}"
                                 class="max-h-10 w-auto max-w-[140px] object-contain"
                                 loading="lazy" decoding="async">
                        </span>

                        <p class="mt-6 grow text-[15px] leading-relaxed">{{ $card['text'] }}</p>

                        <span class="link-arrow mt-6">
                            Learn more
                            <x-icon name="arrow" class="h-4 w-4 transition group-hover:translate-x-1" />
                        </span>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    <x-capabilities class="bg-white" />

    {{-- ============================ OUR CUSTOMERS =========================== --}}
    <section class="bg-white py-20 sm:py-24">
        <div class="container-page">
            <x-section-heading eyebrow="Trusted by industry leaders" title="Our Customers" />

            <div class="marquee fade-x mt-12 overflow-hidden py-4" aria-label="Selected customers">
                {{-- pr-5 matches the gap, so each half is the same width and the loop doesn't jump. --}}
                <div class="marquee-track flex w-max gap-5 pr-5">
                    {{-- Duplicated once so the loop is seamless. --}}
                    @foreach (array_merge(config('site.customers'), config('site.customers')) as $customer)
                        <div class="group flex h-24 w-52 shrink-0 items-center justify-center rounded-xl border border-line bg-white px-6 shadow-card transition hover:border-brand-200">
                            <img src="{{ asset($customer['logo']) }}" alt="{{ $customer['name'] }}"
                                 class="max-h-14 w-auto max-w-full object-contain"
                                 loading="lazy" decoding="async">
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <x-feature-banner class="bg-white" />

    <x-industries class="bg-surface" />

    <x-whats-happening class="bg-white" />

    {{-- ============================== OUR STORY ============================= --}}
    {{--
        A white rounded card floating on the tinted, diagonally-streaked
        background. Copy left, photograph right, key figures along the foot.
    --}}
    <section id="story" class="relative overflow-hidden bg-[#eceef7] py-20 sm:py-24">
        <img src="{{ asset(config('site.services_band_image')) }}" alt=""
             class="bg-streaks opacity-[0.22]" loading="lazy" decoding="async" aria-hidden="true">

        <div class="container-page relative">
            <div class="reveal overflow-hidden rounded-2xl bg-white shadow-float">
                <div class="grid grid-cols-1 items-stretch md:grid-cols-2">
                    <div class="min-w-0 px-6 py-10 sm:px-10 lg:px-14 lg:py-16">
                        <x-section-heading eyebrow="Who we are" title="Our Story" align="left" />

                        <div class="mt-8 space-y-4">
                            @foreach (config('site.story') as $paragraph)
                                <p class="text-[15px] leading-[1.8]">{{ $paragraph }}</p>
                            @endforeach
                        </div>

                        <a href="{{ route('about') }}" class="btn-ghost mt-8">
                            Read our story
                            <x-icon name="arrow" class="h-4 w-4" />
                        </a>
                    </div>

                    {{-- Hidden below md, as on the source. --}}
                    <div class="relative hidden min-w-0 md:block">
                        <img src="{{ asset(config('site.story_image')) }}"
                             alt="The Berger Tech and Berger Paints IT teams at Tech Team Day by the Padma river"
                             class="absolute inset-0 h-full w-full object-cover"
                             loading="lazy" decoding="async">
                    </div>
                </div>

                <dl class="grid grid-cols-2 divide-line border-t border-line bg-surface lg:grid-cols-4 lg:divide-x">
                    @foreach ([
                        ['value' => config('site.founded'), 'label' => 'Founded'],
                        ['value' => '12+', 'label' => 'Years of expertise', 'count' => 12, 'suffix' => '+'],
                        ['value' => (floor(count(config('site.customers')) / 10) * 10).'+', 'label' => 'Enterprise customers', 'count' => floor(count(config('site.customers')) / 10) * 10, 'suffix' => '+'],
                        ['value' => count(config('site.service_cards')), 'label' => 'Technology partners', 'count' => count(config('site.service_cards')), 'suffix' => ''],
                    ] as $stat)
                        <div class="flex flex-col-reverse px-6 py-6 text-center sm:py-8">
                            <dt class="mt-1 text-[13px] font-semibold tracking-wide text-muted uppercase">{{ $stat['label'] }}</dt>
                            <dd class="text-gradient font-display text-3xl font-extrabold tracking-[-0.02em] sm:text-[40px]"
                                @isset($stat['count']) data-count="{{ $stat['count'] }}" data-suffix="{{ $stat['suffix'] }}" @endisset>{{ $stat['value'] }}</dd>
                        </div>
                    @endforeach
                </dl>
            </div>
        </div>
    </section>

    {{-- ========================= WHY WE ARE PREMIER ========================= --}}
    {{-- Same soft pastel style as the industries section: tinted leaf icon blobs, cycled per card. --}}
    @php
        $whyTints = [
            ['tile' => 'bg-sky text-sky-ink', 'number' => 'text-sky-ink/50'],
            ['tile' => 'bg-lilac text-lilac-ink', 'number' => 'text-lilac-ink/50'],
            ['tile' => 'bg-mint text-mint-ink', 'number' => 'text-mint-ink/50'],
            ['tile' => 'bg-blush text-blush-ink', 'number' => 'text-blush-ink/50'],
            ['tile' => 'bg-sand text-sand-ink', 'number' => 'text-sand-ink/50'],
        ];
    @endphp
    <section class="relative overflow-hidden bg-surface py-20 font-display sm:py-24">
        <div class="guide-lines pointer-events-none absolute inset-0" aria-hidden="true"></div>

        <div class="container-page relative">
            <x-section-heading
                eyebrow="Why Berger Tech"
                title="Why We Are Premier Choice"
                highlight="Premier Choice"
                lead="Eight reasons organizations across Bangladesh trust us with the systems their business runs on."
                :soft="true"
            />

            <div class="mt-14 grid gap-4 sm:grid-cols-2 lg:grid-cols-4 lg:gap-5">
                @foreach (config('site.why_us') as $index => $reason)
                    @php($tint = $whyTints[$index % count($whyTints)])
                    <div class="reveal card card-hover group relative flex flex-col gap-5 p-6">
                        <span class="absolute top-5 right-6 text-sm font-semibold {{ $tint['number'] }}">
                            {{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}
                        </span>
                        <span class="leaf-diag flex h-16 w-16 shrink-0 items-center justify-center transition duration-300 group-hover:scale-105 {{ $tint['tile'] }}">
                            <x-mask-icon :src="$reason['icon']" class="h-9 w-9" />
                        </span>
                        <p class="text-[16px] leading-snug font-medium text-ink">{{ $reason['text'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- =========================== HOW WE ENGAGE ============================ --}}
    <section class="relative overflow-hidden bg-white py-20 sm:py-28">
        <div class="bg-mesh pointer-events-none absolute inset-0" aria-hidden="true"></div>

        <div class="container-page relative">
            <x-section-heading
                eyebrow="How we engage"
                title="From first call to go-live"
                highlight="go-live"
                lead="A clear, four-step path — so you always know what happens next and who is accountable for it."
            />

            <ol class="relative mt-16 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                {{-- Connector line behind the step numbers on wide screens. --}}
                <span class="absolute top-7 right-[12%] left-[12%] hidden border-t-2 border-dashed border-brand-200 lg:block" aria-hidden="true"></span>

                @foreach (config('site.process') as $step)
                    <li class="reveal relative flex flex-col text-center">
                        <span class="relative mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-gradient-to-br from-brand-600 to-[#7c5cff] font-display text-lg font-extrabold text-white shadow-[0_0_0_8px_#fff,0_14px_30px_-10px_rgb(31_39_80/0.6)]">
                            {{ $loop->iteration }}
                        </span>
                        <div class="card card-hover mt-6 grow p-6">
                            <h3 class="font-display text-xl font-bold text-ink">{{ $step['title'] }}</h3>
                            <p class="mt-3 text-sm leading-relaxed">{{ $step['text'] }}</p>
                        </div>
                    </li>
                @endforeach
            </ol>

            <div class="mt-14 text-center">
                <a href="{{ route('appointment') }}" class="btn-lilac group">
                    Start with a consultation
                    <x-icon name="arrow" class="h-4 w-4 transition group-hover:translate-x-0.5" />
                </a>
            </div>
        </div>
    </section>

    <x-tech-stack class="bg-surface" />

    {{-- ============================ OUR SOLUTIONS =========================== --}}
    <section id="solutions" class="bg-white py-20 sm:py-28">
        <div class="container-page">
            <x-section-heading
                eyebrow="Ready-made accelerators"
                title="Our Solutions"
                lead="Configurable solutions that remove friction from delivery, logistics, procurement and sales."
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
                            <h3 class="font-display text-lg font-bold text-ink transition group-hover:text-brand-600">
                                {{ $solution['name'] }}
                            </h3>
                            <p class="mt-3 grow text-sm leading-relaxed">
                                {{ \Illuminate\Support\Str::limit($solution['card'], 130) }}
                            </p>
                            <span class="link-arrow mt-5">
                                Explore
                                <x-icon name="arrow" class="h-4 w-4 transition group-hover:translate-x-1" />
                            </span>
                        </div>
                    </a>
                @endforeach

                <div class="reveal relative flex min-h-[320px] flex-col justify-end overflow-hidden rounded-xl p-8 text-white shadow-card">
                    <img src="{{ asset('images/solutions/innovation.jpg') }}" alt=""
                         class="absolute inset-0 h-full w-full object-cover"
                         loading="lazy" decoding="async" aria-hidden="true">
                    <div class="absolute inset-0 bg-gradient-to-t from-brand-950/95 to-brand-600/80" aria-hidden="true"></div>

                    <div class="relative">
                        <h3 class="font-display text-xl font-bold text-white">The right place for solution and innovation</h3>
                        <p class="mt-3 text-sm text-white/85">
                            Tell us the process you want to fix and we will show you what it looks like running.
                        </p>
                        <a href="{{ route('appointment') }}" class="btn-white mt-6 w-fit">Set an Appointment</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================== CULTURE =============================== --}}
    {{--
        The navy-to-blue brand band floating on the tinted, streaked
        background. Heading and a two-photo collage from Tech Team Day on the
        left; the six values as frosted icon tiles on the right.
    --}}
    <section class="relative overflow-hidden bg-[#f3f4fa] py-16 sm:py-20">
        <img src="{{ asset(config('site.services_band_image')) }}" alt=""
             class="bg-streaks" loading="lazy" decoding="async" aria-hidden="true">

        <div class="container-page relative">
            <div class="reveal bg-brand-band relative overflow-hidden rounded-3xl text-white shadow-float">
                <div class="grid-lines-dark pointer-events-none absolute inset-0" aria-hidden="true"></div>
                <div class="pointer-events-none absolute -top-24 -right-16 h-96 w-96 rounded-full bg-brand-400/40 blur-3xl" aria-hidden="true"></div>
                <div class="pointer-events-none absolute -bottom-32 -left-24 h-96 w-96 rounded-full bg-[#7c5cff]/30 blur-3xl" aria-hidden="true"></div>

                <div class="relative grid gap-12 px-6 py-12 sm:px-10 sm:py-16 lg:grid-cols-12 lg:gap-14 lg:px-14 lg:py-20">
                    <div class="flex min-w-0 flex-col lg:col-span-5">
                        <p class="eyebrow eyebrow-dark w-fit">Our culture</p>
                        <h2 class="mt-5 font-display text-[32px] leading-[1.08] font-extrabold tracking-[-0.03em] text-white sm:text-[40px] lg:text-[44px]">
                            The right place for <span class="text-gradient-light">solution and innovation</span>
                        </h2>
                        <p class="mt-5 max-w-md text-[15px] leading-relaxed text-white/75 sm:text-base">
                            Nurturing talent and building a thriving workplace, where two Berger teams work, learn and celebrate as one.
                        </p>

                        {{-- Collage: the main photo stretches to the tiles' height on lg; the second sits over its top-right corner. --}}
                        <div class="relative mt-10 pt-8 pr-6 sm:pr-10 lg:flex lg:grow lg:flex-col">
                            <figure class="relative overflow-hidden rounded-2xl shadow-float ring-1 ring-white/20 lg:min-h-[360px] lg:grow">
                                <img src="{{ asset(config('site.culture_photos')[0]) }}"
                                     alt="The Berger Tech and Berger Paints IT teams together at Tech Team Day"
                                     class="aspect-4/3 w-full object-cover object-[center_75%] lg:absolute lg:inset-0 lg:aspect-auto lg:h-full"
                                     loading="lazy" decoding="async">

                                <figcaption class="glass-card absolute inset-x-3 bottom-3 flex items-center gap-3 p-3 sm:right-auto">
                                    <span class="icon-tile h-10 w-10 rounded-xl"><x-icon name="calendar" class="h-5 w-5" /></span>
                                    <span class="min-w-0">
                                        <span class="block font-display text-[13px] font-bold text-ink">Tech Team Day 2026</span>
                                        <span class="block text-[12px] text-muted">Away from screens, closer to the team</span>
                                    </span>
                                </figcaption>
                            </figure>

                            <img src="{{ asset(config('site.culture_photos')[1]) }}" alt=""
                                 class="float-slow absolute top-0 right-0 aspect-4/3 w-[40%] rounded-xl object-cover shadow-float ring-4 ring-white"
                                 loading="lazy" decoding="async" aria-hidden="true">
                        </div>
                    </div>

                    <ul class="grid content-center gap-4 sm:grid-cols-2 lg:col-span-7">
                        @foreach (config('site.culture') as $value)
                            <li class="reveal group relative overflow-hidden rounded-2xl bg-gradient-to-b from-white/[0.10] to-white/[0.03] p-6 ring-1 ring-white/10 backdrop-blur-sm hover:-translate-y-1 hover:ring-white/30">
                                <span class="pointer-events-none absolute inset-x-6 top-0 h-px bg-gradient-to-r from-transparent via-white/40 to-transparent" aria-hidden="true"></span>
                                <span class="pointer-events-none absolute top-4 right-5 font-display text-5xl leading-none font-extrabold text-white/[0.07] transition group-hover:text-white/15" aria-hidden="true">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>

                                <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-gradient-to-br from-brand-500 to-[#7c5cff] text-white shadow-[0_10px_24px_-10px_rgb(124_92_255/0.8)] ring-1 ring-white/20 transition group-hover:scale-110">
                                    <x-icon :name="$value['icon']" class="h-5 w-5" />
                                </span>
                                <h3 class="mt-5 font-display text-lg leading-tight font-bold text-white">{{ $value['title'] }}</h3>
                                <p class="mt-2 text-[14px] leading-relaxed text-white/70">{{ $value['text'] }}</p>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <x-faq class="bg-white" />

    {{-- ========================= BOOK FOR CONSULTATION ====================== --}}
    {{--
        One full-width booking panel. Keeps id="contact", which the header's
        Contact Us button points at. The source embeds a third-party
        scheduling iframe here; this links to the app's own booking flow.
    --}}
    <section id="contact" class="relative overflow-hidden bg-surface py-20 sm:py-24">
        <div class="bg-mesh pointer-events-none absolute inset-0" aria-hidden="true"></div>

        <div class="container-page relative">
            <div class="reveal relative overflow-hidden rounded-3xl bg-brand-950 text-white shadow-float">
                <div class="grid-lines-dark pointer-events-none absolute inset-0" aria-hidden="true"></div>
                <div class="pointer-events-none absolute -top-32 -left-24 h-96 w-96 rounded-full bg-brand-600/50 blur-3xl" aria-hidden="true"></div>
                <div class="pointer-events-none absolute -right-24 -bottom-32 h-96 w-96 rounded-full bg-[#7c5cff]/30 blur-3xl" aria-hidden="true"></div>

                <div class="relative grid gap-12 p-8 sm:p-12 lg:grid-cols-[1.1fr_1fr] lg:items-center lg:gap-16 lg:p-16">
                    <div>
                        <p class="eyebrow eyebrow-dark">Let&rsquo;s talk</p>
                        <h2 id="appointment" class="mt-5 scroll-mt-28 font-display text-[34px] leading-[1.08] font-extrabold tracking-[-0.03em] text-white sm:text-[44px] lg:text-[52px]">
                            Book for <span class="text-gradient-light">Consultation</span>
                        </h2>
                        <p class="mt-5 max-w-xl text-base leading-relaxed text-white/75 sm:text-lg">
                            A {{ config('site.booking.duration') }}-minute conversation with a consultant about SAP,
                            Microsoft Dynamics 365, cloud, automation or security — online or at Berger House.
                        </p>

                        <ul class="mt-8 grid gap-3 sm:grid-cols-3">
                            @foreach (['Pick a time that suits you', 'No preparation needed', 'Confirmed by email'] as $point)
                                <li class="flex items-center gap-2.5 text-[14px] text-white/85">
                                    <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-white/10 ring-1 ring-white/20">
                                        <x-icon name="check" class="h-3.5 w-3.5 text-brand-200" />
                                    </span>
                                    {{ $point }}
                                </li>
                            @endforeach
                        </ul>

                        <div class="mt-10 flex flex-wrap gap-3">
                            <a href="{{ route('appointment') }}" class="btn-outline">
                                <x-icon name="calendar" class="h-4 w-4" />
                                Set an Appointment
                            </a>
                            <a href="tel:{{ config('site.contact.phone_link') }}" class="btn-outline">
                                <x-icon name="phone" class="h-4 w-4" />
                                Call Now
                            </a>
                        </div>
                    </div>

                    {{-- The meeting at a glance. --}}
                    <div class="rounded-2xl bg-white/[0.06] p-6 ring-1 ring-white/15 backdrop-blur-sm sm:p-8">
                        <p class="font-display text-lg font-bold text-white">{{ config('site.booking.title') }}</p>
                        <p class="mt-1 text-[13px] text-white/60">What to expect</p>

                        <dl class="mt-6 divide-y divide-white/10">
                            @foreach ([
                                ['icon' => 'clock', 'label' => 'Duration', 'value' => config('site.booking.duration').' minutes'],
                                ['icon' => 'video', 'label' => 'Format', 'value' => 'Online or at Berger House'],
                                ['icon' => 'calendar', 'label' => 'Available', 'value' => 'Sunday – Thursday, 9:00 AM – 6:00 PM'],
                                ['icon' => 'globe', 'label' => 'Time zone', 'value' => config('site.booking.timezone_label')],
                            ] as $detail)
                                <div class="flex items-center gap-4 py-4 first:pt-0 last:pb-0">
                                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white/10 text-brand-200">
                                        <x-icon :name="$detail['icon']" class="h-5 w-5" />
                                    </span>
                                    <div>
                                        <dt class="text-[12px] font-semibold tracking-[0.12em] text-white/50 uppercase">{{ $detail['label'] }}</dt>
                                        <dd class="mt-0.5 text-[15px] text-white">{{ $detail['value'] }}</dd>
                                    </div>
                                </div>
                            @endforeach
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection
