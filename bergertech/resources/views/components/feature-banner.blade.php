@props([
    'banner' => config('site.banner'),
])

@php
    // Escape first, then pick out the highlighted phrase in brand blue (as in x-section-heading's soft variant).
    $title = e($banner['title']);
    if (! empty($banner['highlight'])) {
        $title = str_replace(e($banner['highlight']), '<span class="text-brand-600">'.e($banner['highlight']).'</span>', $title);
    }
@endphp

{{--
    Pastel photo banner in the style of the industries section: a sky leaf card
    with the headline and button on the left and the photo set inside on the
    right. Copy lives in config('site.banner').
--}}
<section {{ $attributes->class(['py-12 font-display sm:py-16']) }}>
    <div class="container-page">
        <div class="leaf-tr grid overflow-hidden bg-sky lg:min-h-[440px] lg:grid-cols-[1.05fr_1fr]">

            <div class="flex flex-col justify-center p-7 sm:p-10 lg:p-14">
                <h2 class="text-[32px] leading-[1.1] font-semibold tracking-[-0.025em] text-ink sm:text-5xl lg:text-[52px]">{!! $title !!}</h2>

                <a href="{{ route($banner['url_name']) }}"
                   class="group mt-8 inline-flex items-center gap-2 self-start rounded-full bg-ink px-6 py-3.5 text-[15px] font-semibold text-white transition hover:bg-ink/85 active:translate-y-px sm:mt-10">
                    {{ $banner['cta'] }}
                    <x-icon name="chevron-right" class="h-4 w-4 transition group-hover:translate-x-0.5" />
                </a>
            </div>

            <div class="relative m-3 mt-0 min-h-[240px] overflow-hidden rounded-[1.25rem] sm:min-h-[320px] lg:mt-3 lg:ml-0 lg:rounded-tl-[6rem] lg:rounded-tr-[5.25rem]">
                <img src="{{ asset($banner['image']) }}" alt=""
                     class="absolute inset-0 h-full w-full object-cover object-[center_65%]"
                     width="2560" height="1707" loading="lazy" decoding="async">
                <div class="absolute inset-x-0 bottom-0 h-1/2 bg-gradient-to-t from-brand-950/60 to-transparent" aria-hidden="true"></div>
                <img src="{{ asset(config('site.logo_footer')) }}" alt="{{ config('site.short_name') }}"
                     class="absolute right-6 bottom-6 h-7 w-auto sm:h-8" width="1977" height="425" loading="lazy" decoding="async">
            </div>
        </div>
    </div>
</section>
