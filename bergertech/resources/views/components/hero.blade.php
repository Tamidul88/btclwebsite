@props([
    'title',
    'eyebrow' => null,
    'highlight' => null,
    'subtitle' => null,
    'lines' => [],
    'cta' => null,
    'ctaUrl' => null,
    'secondaryCta' => null,
    'secondaryUrl' => null,
    'image' => null,
    'imagePosition' => 'center',
    'accent' => '#1f2750',
    'breadcrumbs' => [],
    'size' => 'md',
    'eager' => false,
])

@inject('pageStyles', 'App\PageStyles')

@php
    $isLarge = $size === 'lg';
    $glowClass = $pageStyles->classFor("background: {$accent};");
    $imagePositionClass = $pageStyles->classFor("object-position: {$imagePosition};");

    // Escape first, then wrap the highlighted phrase in the gradient span.
    $heading = e($title);
    if ($highlight) {
        $heading = str_replace(e($highlight), '<span class="text-gradient-light">'.e($highlight).'</span>', $heading);
    }
@endphp

{{--
    Deep navy page header: copy on the left, the photograph framed on the
    right. The accent colour tints the main glow, so each service and
    solution page carries its own hue. Inner pages (size "md") drop the
    photograph below lg to keep the header compact on phones.
--}}
<section class="relative overflow-hidden bg-brand-950 text-white">
    <div class="pointer-events-none absolute -top-48 -left-40 h-[36rem] w-[36rem] rounded-full opacity-40 blur-3xl {{ $glowClass }}"
         aria-hidden="true"></div>
    <div class="pointer-events-none absolute top-1/4 -right-48 h-[32rem] w-[32rem] rounded-full bg-[#7c5cff]/25 blur-3xl" aria-hidden="true"></div>
    <div class="grid-lines-dark pointer-events-none absolute inset-0" aria-hidden="true"></div>

    <div @class([
        'container-page relative grid items-center gap-12 pt-32 sm:pt-36',
        'pb-16 lg:grid-cols-[1.08fr_1fr] lg:gap-16 lg:pt-40 lg:pb-28' => $isLarge,
        'pb-14 lg:grid-cols-[1.15fr_1fr] lg:gap-14 lg:pb-20' => ! $isLarge,
    ])>
        <div class="hero-in min-w-0">
            @if ($breadcrumbs)
                <nav aria-label="Breadcrumb" class="mb-6">
                    <ol class="flex flex-wrap items-center gap-1.5 text-[13px] text-white/55">
                        @foreach ($breadcrumbs as $crumb)
                            <li class="flex items-center gap-1.5">
                                @unless ($loop->first)
                                    <x-icon name="chevron-right" class="h-3.5 w-3.5 text-white/30" />
                                @endunless
                                @if (! empty($crumb['url']))
                                    <a href="{{ $crumb['url'] }}" class="transition hover:text-white">{{ $crumb['label'] }}</a>
                                @else
                                    <span class="text-white/85" aria-current="page">{{ $crumb['label'] }}</span>
                                @endif
                            </li>
                        @endforeach
                    </ol>
                </nav>
            @endif

            @if ($eyebrow)
                <p class="eyebrow eyebrow-dark">{{ $eyebrow }}</p>
            @endif

            <h1 @class([
                'font-display font-extrabold tracking-[-0.035em] text-white',
                'mt-5' => $eyebrow,
                'text-[44px] leading-[1.02] sm:text-6xl lg:text-[76px]' => $isLarge,
                'text-4xl leading-[1.08] sm:text-5xl lg:text-[56px]' => ! $isLarge,
            ])>{!! $heading !!}</h1>

            @if ($subtitle)
                <p class="mt-6 max-w-xl text-base leading-relaxed text-white/75 sm:text-lg">{{ $subtitle }}</p>
            @endif

            @foreach ($lines as $line)
                <p class="mt-4 max-w-xl text-[15px] leading-relaxed text-white/70 sm:text-base">{{ $line }}</p>
            @endforeach

            @if ($cta || $secondaryCta)
                <div class="mt-9 flex flex-wrap gap-3">
                    @if ($cta)
                        <a href="{{ $ctaUrl }}" class="btn-sky group">
                            {{ $cta }}
                            <x-icon name="arrow" class="h-4 w-4 transition group-hover:translate-x-0.5" />
                        </a>
                    @endif
                    @if ($secondaryCta)
                        <a href="{{ $secondaryUrl }}" class="btn-outline">{{ $secondaryCta }}</a>
                    @endif
                </div>
            @endif

            @if ($slot->isNotEmpty())
                <div class="mt-12">{{ $slot }}</div>
            @endif
        </div>

        @if ($image)
            <div @class(['hero-in relative min-w-0 [animation-delay:.15s]', 'hidden lg:block' => ! $isLarge])>
                <div class="pointer-events-none absolute -inset-6 rounded-[40px] bg-gradient-to-br from-brand-500/40 via-transparent to-[#7c5cff]/40 blur-2xl" aria-hidden="true"></div>

                <div class="relative overflow-hidden rounded-[28px] ring-1 ring-white/15 shadow-[0_40px_80px_-30px_rgb(0_0_0/0.65)]">
                    <img src="{{ asset($image) }}" alt=""
                         class="aspect-[4/3] w-full object-cover {{ $imagePositionClass }}"
                         loading="{{ $eager ? 'eager' : 'lazy' }}"
                         fetchpriority="{{ $eager ? 'high' : 'auto' }}"
                         decoding="async">
                    <div class="absolute inset-0 bg-gradient-to-t from-brand-950/40 via-transparent to-transparent" aria-hidden="true"></div>
                </div>

                {{ $visual ?? '' }}
            </div>
        @endif
    </div>

    <div class="absolute inset-x-0 bottom-0 h-px bg-gradient-to-r from-transparent via-white/20 to-transparent" aria-hidden="true"></div>
</section>
