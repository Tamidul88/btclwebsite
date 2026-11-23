@php
    // Transparent over a hero, solid from the first pixel on inner pages without one.
    $transparent = $transparentHeader ?? false;

    // Over a full-screen photo (x-hero-cover) the bar gets a dark see-through fill until the page scrolls.
    $cover = $coverHeader ?? false;

    $navItem = 'rounded-full px-3.5 py-2 font-display text-[15px] font-medium transition';

    $menus = [
        ['key' => 'services', 'label' => 'Our Services', 'active' => request()->routeIs('services*')],
        ['key' => 'solutions', 'label' => 'Our Solutions', 'active' => request()->routeIs('solutions*')],
        ['key' => 'resources', 'label' => 'Resources', 'active' => request()->routeIs('resources', 'insights*')],
    ];
@endphp

{{-- Reading progress along the very top of the window, driven by header.js. --}}
<div id="scroll-progress"
     class="pointer-events-none fixed inset-x-0 top-0 z-[55] h-[3px] origin-left scale-x-0 bg-gradient-to-r from-brand-500 via-[#7c5cff] to-brand-300"
     aria-hidden="true"></div>

{{--
    Full width and transparent over a hero. Once the page scrolls (or from
    the start on pages without a hero) the bar condenses into a floating
    glass pill; header.js flips data-scrolled.
--}}
<header id="site-header"
        data-transparent="{{ $transparent ? 'true' : 'false' }}"
        data-scrolled="{{ $transparent ? 'false' : 'true' }}"
        data-cover="{{ $cover ? 'true' : 'false' }}"
        class="group/header fixed inset-x-0 top-0 z-50 transition-[padding,background-color] duration-300 data-[scrolled=true]:pt-3
               data-[cover=true]:data-[scrolled=false]:bg-black/45">

    <div class="container-page">
        <div class="relative flex h-[72px] items-center justify-between gap-3 rounded-2xl transition-all duration-300 sm:gap-4
                    group-data-[scrolled=true]/header:h-16 group-data-[scrolled=true]/header:bg-brand-950/90 group-data-[scrolled=true]/header:px-3
                    group-data-[scrolled=true]/header:shadow-[0_20px_50px_-20px_rgb(7_15_61/0.6)] group-data-[scrolled=true]/header:ring-1
                    group-data-[scrolled=true]/header:ring-white/10 group-data-[scrolled=true]/header:backdrop-blur-xl
                    sm:group-data-[scrolled=true]/header:px-5">

            <a href="{{ route('home') }}" class="flex min-w-0 items-center" aria-label="{{ config('site.name') }} — home">
                <img src="{{ asset(config('site.logo')) }}"
                     alt="{{ config('site.name') }}"
                     class="h-7 w-auto max-w-[170px] object-contain transition-all duration-300 sm:h-11 sm:max-w-none sm:group-data-[scrolled=true]/header:h-9"
                     width="1072" height="231" fetchpriority="high" decoding="async">
            </a>

            {{--
                Desktop navigation. Our Services, Our Solutions and Resources
                open dropdown panels on hover, click or keyboard (header.js).
                Each panel is positioned against this bar, so it spans the
                full width of the header. Below lg the hamburger opens the
                full-screen menu instead.
            --}}
            <nav class="ml-auto hidden items-center gap-1 lg:flex" aria-label="Main">
                <a href="{{ route('about') }}"
                   @class([
                       $navItem,
                       'text-white/90 hover:bg-white/10 hover:text-white' => ! request()->routeIs('about'),
                       'bg-white/10 text-white' => request()->routeIs('about'),
                   ])>
                    Bergertech Story
                </a>

                @foreach ($menus as $menu)
                    <div data-dropdown>
                        <button type="button"
                                data-dropdown-trigger
                                aria-expanded="false"
                                aria-controls="nav-panel-{{ $menu['key'] }}"
                                @class([
                                    $navItem,
                                    'group flex items-center gap-1.5 aria-expanded:bg-white/10 aria-expanded:text-white',
                                    'text-white/90 hover:bg-white/10 hover:text-white' => ! $menu['active'],
                                    'bg-white/10 text-white' => $menu['active'],
                                ])>
                            {{ $menu['label'] }}
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                                 class="h-3 w-3 transition-transform duration-200 group-aria-expanded:rotate-180"
                                 aria-hidden="true">
                                <path d="m6 9 6 6 6-6" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>

                        <div id="nav-panel-{{ $menu['key'] }}" data-dropdown-panel class="dropdown-panel absolute inset-x-0 top-full pt-3">
                            <div class="overflow-hidden rounded-2xl bg-white text-left text-ink shadow-[0_30px_80px_-20px_rgb(7_15_61/0.45)] ring-1 ring-black/5">
                                @include('partials.nav-'.$menu['key'])
                            </div>
                        </div>
                    </div>
                @endforeach
            </nav>

            <div class="flex shrink-0 items-center gap-2">
                {{-- Brand blue over the photo, pastel sky once the bar behind it is navy. --}}
                <a href="{{ route('home') }}#contact"
                   class="btn-sky h-10 shrink-0 px-3.5 py-0 text-[12px] tracking-normal whitespace-nowrap normal-case sm:px-5 sm:text-[14px]
                          group-data-[cover=true]/header:group-data-[scrolled=false]/header:bg-brand-600
                          group-data-[cover=true]/header:group-data-[scrolled=false]/header:text-white
                          group-data-[cover=true]/header:group-data-[scrolled=false]/header:hover:bg-brand-700">Contact Us</a>

                {{-- Hamburger: mobile and tablet only. --}}
                <button type="button"
                        id="menu-toggle"
                        aria-controls="mega-menu"
                        aria-expanded="false"
                        class="flex h-11 w-11 flex-col items-center justify-center gap-[5px] rounded-full text-white transition hover:bg-white/10 lg:hidden">
                    <span class="sr-only">Open menu</span>
                    <span class="block h-[3px] w-6 rounded bg-current transition-transform duration-300" data-bar="top"></span>
                    <span class="block h-[3px] w-6 rounded bg-current transition-opacity duration-200" data-bar="mid"></span>
                    <span class="block h-[3px] w-6 rounded bg-current transition-transform duration-300" data-bar="bottom"></span>
                </button>
            </div>
        </div>
    </div>
</header>

{{-- Full screen mega menu --}}
<div id="mega-menu"
     hidden
     class="fixed inset-0 z-[60] overflow-y-auto bg-brand-950/[0.97] opacity-0 backdrop-blur-md transition-opacity duration-300">

    <div class="container-page py-8">

        <div class="flex items-center justify-between gap-4">
            <a href="{{ route('home') }}" class="flex items-center">
                <img src="{{ asset(config('site.logo')) }}" alt="{{ config('site.name') }}"
                     class="h-10 w-auto" width="1072" height="231" decoding="async">
            </a>
            <button type="button" id="menu-close"
                    class="flex h-11 w-11 items-center justify-center rounded-full text-white ring-1 ring-white/20 transition hover:bg-white/10">
                <span class="sr-only">Close menu</span>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-6 w-6">
                    <path d="M6 6l12 12M18 6L6 18" stroke-linecap="round"/>
                </svg>
            </button>
        </div>

        <nav class="mt-10 grid gap-10 lg:grid-cols-[1fr_320px]" aria-label="Main">

            {{-- Services --}}
            <div>
                <p class="eyebrow eyebrow-dark">Our Services</p>
                <h2 class="mt-2 font-display text-2xl font-bold text-white">Services</h2>

                <div class="mt-6 grid gap-x-8 gap-y-7 sm:grid-cols-2 xl:grid-cols-3">
                    @foreach (config('site.menu_services') as $block)
                        <div>
                            <h3 class="font-display text-[13px] font-bold tracking-[0.12em] text-brand-300 uppercase">
                                {{ $block['title'] }}
                            </h3>

                            @foreach ($block['groups'] as $group)
                                @if (! empty($group['label']))
                                    <p class="mt-3 font-display text-sm font-semibold text-white">
                                        @isset($group['slug'])
                                            <a href="{{ route('services.show', $group['slug']) }}" class="hover:text-brand-300">{{ $group['label'] }}</a>
                                        @else
                                            {{ $group['label'] }}
                                        @endisset
                                    </p>
                                @endif

                                <ul class="mt-2 space-y-1.5">
                                    @foreach ($group['items'] as $item)
                                        <li>
                                            <a href="{{ isset($group['slug']) ? route('services.show', $group['slug']) : route('services.index') }}"
                                               class="block text-[13px] leading-snug text-white/65 transition hover:text-white">
                                                {{ $item }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Solutions + quick links --}}
            <div class="lg:border-l lg:border-white/10 lg:pl-10">
                <p class="eyebrow eyebrow-dark">Our Solutions</p>
                <h2 class="mt-2 font-display text-2xl font-bold text-white">Solutions</h2>

                <ul class="mt-6 space-y-4">
                    @foreach (config('solutions') as $slug => $solution)
                        <li>
                            <a href="{{ route('solutions.show', $slug) }}" class="group block">
                                <span class="font-display text-sm font-semibold text-white transition group-hover:text-brand-300">
                                    {{ $solution['name'] }}
                                </span>
                                <span class="mt-1 block text-[13px] leading-snug text-white/55">
                                    {{ \Illuminate\Support\Str::limit($solution['card'], 96) }}
                                </span>
                            </a>
                        </li>
                    @endforeach
                </ul>

                <div class="mt-8 border-t border-white/10 pt-6">
                    <ul class="space-y-2.5">
                        <li><a href="{{ route('about') }}" class="font-display text-sm font-semibold text-white hover:text-brand-300">Bergertech Story</a></li>
                        <li><a href="{{ route('insights.index') }}" class="font-display text-sm font-semibold text-white hover:text-brand-300">Insights</a></li>
                        <li><a href="{{ route('resources') }}" class="font-display text-sm font-semibold text-white hover:text-brand-300">Resource Center</a></li>
                        <li><a href="{{ route('appointment') }}" class="font-display text-sm font-semibold text-white hover:text-brand-300">Set an Appointment</a></li>
                        <li><a href="{{ route('contact') }}" class="font-display text-sm font-semibold text-white hover:text-brand-300">Contact Us</a></li>
                    </ul>
                </div>

                <div class="mt-8 rounded-xl bg-white/5 p-5 text-[13px] text-white/70 ring-1 ring-white/10">
                    <p class="font-display font-semibold text-white">To connect with us</p>
                    <p class="mt-2">{{ implode(', ', config('site.contact.address_lines')) }}</p>
                    <p class="mt-2">
                        <a href="tel:{{ config('site.contact.phone_link') }}" class="hover:text-white">{{ config('site.contact.phone') }}</a><br>
                        <a href="mailto:{{ config('site.contact.email') }}" class="hover:text-white">{{ config('site.contact.email') }}</a>
                    </p>
                </div>
            </div>
        </nav>
    </div>
</div>
