@props([
    'title',
    'lines' => [],
    'cta' => null,
    'ctaUrl' => null,
    'image',
    'imagePosition' => 'center',
])

@inject('pageStyles', 'App\PageStyles')

@php
    $imagePositionClass = $pageStyles->classFor("object-position: {$imagePosition};");
@endphp

{{--
    Full-screen photograph with the copy on top, used on the home page. Pair it
    with 'coverHeader' => true on the layout for the see-through header bar.
    Inner pages use x-hero instead.
--}}
<section data-hero-cover class="relative isolate flex min-h-svh items-center overflow-hidden bg-black text-white">
    <img src="{{ asset($image) }}" alt=""
         class="absolute inset-0 -z-10 h-full w-full object-cover {{ $imagePositionClass }}"
         width="2560" height="1440" loading="eager" fetchpriority="high" decoding="async">

    {{-- An even shade over the photo, then a deeper one behind the copy. --}}
    <div class="absolute inset-0 -z-10 bg-black/55 sm:bg-black/30" aria-hidden="true"></div>
    <div class="absolute inset-0 -z-10 bg-gradient-to-r from-black/60 via-black/35 via-45% to-transparent to-75%" aria-hidden="true"></div>

    <div class="container-page w-full pt-32 pb-20">
        <div class="hero-in max-w-2xl">
            <h1 class="max-w-md font-display text-[44px] leading-[1.04] font-extrabold tracking-[-0.03em] text-white sm:text-6xl lg:text-[68px]">{{ $title }}</h1>

            <div class="mt-7 max-w-xl space-y-4">
                @foreach ($lines as $line)
                    <p class="text-base leading-relaxed text-white sm:text-[17px]">{{ $line }}</p>
                @endforeach
            </div>

            @if ($cta)
                <a href="{{ $ctaUrl }}" class="btn-primary mt-9 px-7 py-3.5 text-[15px] tracking-normal normal-case shadow-none">{{ $cta }}</a>
            @endif
        </div>
    </div>
</section>
