@php
    $logos = collect(config('site.service_cards'))->pluck('logo', 'slug');
@endphp

<div class="grid grid-cols-[1fr_300px]">
    <div class="p-6">
        <p class="text-[11px] font-bold tracking-[0.16em] text-muted uppercase">Service lines</p>

        <ul class="mt-3 grid grid-cols-2 gap-1">
            @foreach (config('site.menu_services') as $block)
                @php
                    $slug = collect($block['groups'])->pluck('slug')->filter()->first();
                    $url = $block['title'] !== 'ERP as a Service' && $slug
                        ? route('services.show', $slug)
                        : route('services.index').'#'.\Illuminate\Support\Str::slug($block['title']);
                @endphp
                <li>
                    <a href="{{ $url }}" class="group flex gap-3.5 rounded-xl p-3 transition hover:bg-surface">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-brand-50 text-brand-600 transition group-hover:bg-brand-600 group-hover:text-white">
                            <x-icon :name="$block['icon']" class="h-5 w-5" />
                        </span>
                        <span class="min-w-0">
                            <span class="block font-display text-[14px] font-bold text-ink">{{ $block['title'] }}</span>
                            <span class="mt-0.5 line-clamp-2 block text-[12.5px] leading-snug text-muted">{{ $block['summary'] }}</span>
                        </span>
                    </a>
                </li>
            @endforeach
        </ul>
    </div>

    <div class="border-l border-line bg-surface p-6">
        <p class="text-[11px] font-bold tracking-[0.16em] text-muted uppercase">Platforms</p>

        <ul class="mt-3 space-y-1">
            @foreach (config('services_content') as $slug => $service)
                <li>
                    <a href="{{ route('services.show', $slug) }}" class="group flex items-center gap-3 rounded-xl p-2.5 transition hover:bg-white hover:shadow-card">
                        <span class="flex h-10 w-14 shrink-0 items-center justify-center rounded-lg bg-white px-1.5 ring-1 ring-line">
                            <img src="{{ asset($logos[$slug] ?? config('site.logo')) }}" alt="" class="max-h-6 w-auto max-w-full object-contain" loading="lazy" decoding="async">
                        </span>
                        <span class="font-display text-[14px] font-semibold text-ink">{{ $service['name'] }}</span>
                        <x-icon name="arrow" class="ml-auto h-4 w-4 text-brand-600 opacity-0 transition group-hover:opacity-100" />
                    </a>
                </li>
            @endforeach
        </ul>

        <a href="{{ route('services.index') }}" class="link-arrow mt-5 px-2.5">
            All services
            <x-icon name="arrow" class="h-4 w-4" />
        </a>
    </div>
</div>
