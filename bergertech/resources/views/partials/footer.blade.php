{{--
    Two bands on deep navy: the link columns, and a bottom bar with the
    legal line and a back-to-top link.
--}}
<footer class="relative overflow-hidden bg-brand-950 text-[14px] text-white/65">

    <div class="dot-grid pointer-events-none absolute inset-0 opacity-60" aria-hidden="true"></div>
    <div class="pointer-events-none absolute -top-40 left-1/2 h-80 w-[56rem] -translate-x-1/2 rounded-full bg-brand-600/25 blur-3xl" aria-hidden="true"></div>

    <div class="container-page relative">

        {{-- Link columns --}}
        <div class="grid gap-12 pt-20 pb-14 sm:grid-cols-2 sm:pt-24 lg:grid-cols-[1.5fr_1fr_1fr_1fr] lg:gap-10">

            <div class="sm:col-span-2 lg:col-span-1 lg:pr-8">
                <a href="{{ route('home') }}" class="inline-block" aria-label="{{ config('site.name') }} — home">
                    <img src="{{ asset(config('site.logo_footer')) }}"
                         alt="{{ config('site.name') }}"
                         class="h-11 w-auto" width="1977" height="425" loading="lazy" decoding="async">
                </a>

                <p class="mt-6 max-w-sm leading-relaxed">
                    A next-generation technology solutions and services company, simplifying business through
                    SAP, Microsoft Dynamics 365, cloud, automation and security.
                </p>

                <ul class="mt-7 flex gap-2.5">
                    @foreach (config('site.social') as $social)
                        <li>
                            <a href="{{ $social['url'] }}" target="_blank" rel="noopener noreferrer"
                               class="flex h-10 w-10 items-center justify-center rounded-lg bg-white/[0.06] text-white ring-1 ring-white/10 transition hover:-translate-y-0.5 hover:bg-brand-600 hover:ring-brand-600">
                                <span class="sr-only">{{ $social['name'] }}</span>
                                <x-icon :name="$social['icon']" class="h-[18px] w-[18px]" />
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>

            @foreach ([
                [
                    'title' => 'Services',
                    'links' => collect(config('services_content'))
                        ->map(fn ($service, $slug) => ['label' => $service['name'], 'url' => route('services.show', $slug)])
                        ->push(['label' => 'All services', 'url' => route('services.index')]),
                ],
                [
                    'title' => 'Solutions',
                    'links' => collect(config('solutions'))
                        ->map(fn ($solution, $slug) => ['label' => $solution['name'], 'url' => route('solutions.show', $slug)]),
                ],
                [
                    'title' => 'Company',
                    'links' => collect([
                        ['label' => 'Bergertech Story', 'url' => route('about')],
                        ['label' => 'Insights', 'url' => route('insights.index')],
                        ['label' => 'Resource Center', 'url' => route('resources')],
                        ['label' => 'FAQ', 'url' => route('resources').'#faq'],
                        ['label' => 'Contact Us', 'url' => route('contact')],
                        ['label' => 'Set an Appointment', 'url' => route('appointment')],
                    ]),
                ],
            ] as $column)
                <nav aria-label="Footer — {{ $column['title'] }}">
                    <h2 class="font-display text-[13px] font-bold tracking-[0.18em] text-white uppercase">{{ $column['title'] }}</h2>
                    <ul class="mt-5 space-y-3">
                        @foreach ($column['links'] as $link)
                            <li>
                                <a href="{{ $link['url'] }}" class="inline-block transition duration-200 hover:translate-x-1 hover:text-white">{{ $link['label'] }}</a>
                            </li>
                        @endforeach
                    </ul>
                </nav>
            @endforeach
        </div>

        {{-- Bottom bar --}}
        <div class="flex flex-col gap-4 border-t border-white/10 py-6 text-[13px] sm:flex-row sm:items-center sm:justify-between">
            <p>
                &copy; {{ now()->year }} <span class="font-semibold text-white">Berger Tech Consulting Ltd.</span>
                <span class="hidden text-white/45 md:inline">· {{ config('site.tagline') }}</span>
            </p>

            <a href="#top" class="group inline-flex items-center gap-2 self-start font-display text-[12px] font-semibold tracking-wide text-white/80 uppercase transition hover:text-white sm:self-auto">
                Back to top
                <span class="flex h-8 w-8 items-center justify-center rounded-full ring-1 ring-white/20 transition group-hover:bg-brand-600 group-hover:ring-brand-600">
                    <x-icon name="arrow-up" class="h-4 w-4" />
                </span>
            </a>
        </div>
    </div>
</footer>
