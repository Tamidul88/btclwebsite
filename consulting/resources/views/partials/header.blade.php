<header class="sticky top-0 z-40 border-b border-slate-200 bg-white/95 backdrop-blur" x-data="mobileNav">
    <div class="container-page flex h-16 items-center justify-between gap-6">

        <a href="{{ route('home') }}" class="flex items-center gap-2 text-ink">
            <span class="grid h-8 w-8 place-items-center rounded-md bg-brand-600 text-sm font-bold text-white" aria-hidden="true">
                {{ mb_substr(config('site.company'), 0, 1) }}
            </span>
            <span class="text-[15px] font-semibold tracking-tight">{{ config('site.company') }}</span>
        </a>

        <nav class="hidden items-center gap-7 md:flex" aria-label="Main">
            <a href="{{ route('services') }}" class="text-sm font-medium text-slate-600 transition hover:text-brand-700 {{ request()->routeIs('services*') ? 'text-brand-700' : '' }}">Services</a>
            <a href="{{ route('case-studies') }}" class="text-sm font-medium text-slate-600 transition hover:text-brand-700 {{ request()->routeIs('case-studies*') ? 'text-brand-700' : '' }}">Case studies</a>
            <a href="{{ route('about') }}" class="text-sm font-medium text-slate-600 transition hover:text-brand-700 {{ request()->routeIs('about') ? 'text-brand-700' : '' }}">About</a>
            <a href="{{ route('contact') }}" class="text-sm font-medium text-slate-600 transition hover:text-brand-700 {{ request()->routeIs('contact') ? 'text-brand-700' : '' }}">Contact</a>
        </nav>

        <div class="flex items-center gap-2">
            <a href="{{ route('consultation') }}" class="btn-primary hidden md:inline-flex">Request a consultation</a>

            <button type="button" x-on:click="toggle"
                    class="grid h-10 w-10 place-items-center rounded-lg border border-slate-300 md:hidden"
                    aria-label="Toggle navigation" x-bind:aria-expanded="open">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-5 w-5" aria-hidden="true">
                    <path d="M4 7h16M4 12h16M4 17h16" stroke-linecap="round"/>
                </svg>
            </button>
        </div>
    </div>

    {{--
        x-cloak hides this until Alpine boots (rule is in app.css, not an
        inline style). After that, panelClass adds or removes "hidden".

        "hidden" must NOT be in the static class list: Alpine's class binding
        only manages the classes it added itself, so a statically declared
        "hidden" would never be removed and the menu could not open.
    --}}
    <div class="border-t border-slate-200 bg-white md:hidden" x-cloak x-bind:class="panelClass">
        <nav class="container-page flex flex-col py-3" aria-label="Mobile">
            <a href="{{ route('services') }}" class="py-2.5 text-sm font-medium text-slate-700">Services</a>
            <a href="{{ route('case-studies') }}" class="py-2.5 text-sm font-medium text-slate-700">Case studies</a>
            <a href="{{ route('about') }}" class="py-2.5 text-sm font-medium text-slate-700">About</a>
            <a href="{{ route('contact') }}" class="py-2.5 text-sm font-medium text-slate-700">Contact</a>
            <a href="{{ route('consultation') }}" class="btn-primary mt-3">Request a consultation</a>
        </nav>
    </div>
</header>
