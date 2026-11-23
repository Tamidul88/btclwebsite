<footer class="mt-24 border-t border-slate-200 bg-slate-50">
    <div class="container-page grid gap-10 py-14 md:grid-cols-4">

        <div class="md:col-span-2">
            <div class="flex items-center gap-2 text-ink">
                <span class="grid h-8 w-8 place-items-center rounded-md bg-brand-600 text-sm font-bold text-white" aria-hidden="true">
                    {{ mb_substr(config('site.company'), 0, 1) }}
                </span>
                <span class="text-[15px] font-semibold">{{ config('site.company') }}</span>
            </div>
            <p class="mt-4 max-w-sm text-sm leading-relaxed">{{ config('site.tagline') }}. Independent technology consultancy, operating since {{ config('site.founded') }}.</p>
        </div>

        <div>
            <h2 class="text-sm font-semibold text-ink">Services</h2>
            <ul class="mt-4 space-y-2">
                @foreach (config('site.services') as $slug => $service)
                    <li><a href="{{ route('services.show', $slug) }}" class="text-sm transition hover:text-brand-700">{{ $service['name'] }}</a></li>
                @endforeach
            </ul>
        </div>

        <div>
            <h2 class="text-sm font-semibold text-ink">Contact</h2>
            <ul class="mt-4 space-y-2 text-sm">
                <li><a href="mailto:{{ config('site.contact.email') }}" class="transition hover:text-brand-700">{{ config('site.contact.email') }}</a></li>
                <li><a href="tel:{{ preg_replace('/[^0-9+]/', '', config('site.contact.phone')) }}" class="transition hover:text-brand-700">{{ config('site.contact.phone') }}</a></li>
                <li class="pt-2 text-slate-500">
                    {{ config('site.contact.address.street') }}<br>
                    {{ config('site.contact.address.locality') }} {{ config('site.contact.address.postal_code') }}
                </li>
            </ul>
        </div>
    </div>

    <div class="border-t border-slate-200">
        <div class="container-page flex flex-col gap-3 py-6 sm:flex-row sm:items-center sm:justify-between">
            <p class="text-xs text-slate-500">&copy; {{ now()->year }} {{ config('site.company') }}. All rights reserved.</p>
            <ul class="flex gap-5 text-xs text-slate-500">
                <li><a href="{{ route('legal', 'privacy') }}" class="transition hover:text-brand-700">Privacy</a></li>
                <li><a href="{{ route('legal', 'terms') }}" class="transition hover:text-brand-700">Terms</a></li>
                <li><a href="{{ route('legal', 'cookies') }}" class="transition hover:text-brand-700">Cookies</a></li>
            </ul>
        </div>
    </div>
</footer>
