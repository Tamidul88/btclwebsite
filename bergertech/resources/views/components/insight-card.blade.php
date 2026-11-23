@props([
    'insight',
    'featured' => false,
])

<a href="{{ route('insights.show', $insight['slug']) }}"
   {{ $attributes->class([
       'reveal card card-hover group flex overflow-hidden',
       'flex-col lg:min-h-[400px] lg:flex-row' => $featured,
       'flex-col' => ! $featured,
   ]) }}>

    <span @class([
        'block shrink-0 overflow-hidden',
        'aspect-[16/10] lg:aspect-auto lg:w-[55%]' => $featured,
        'aspect-[16/10]' => ! $featured,
    ])>
        <img src="{{ asset($insight['image']) }}" alt=""
             class="h-full w-full object-cover transition duration-500 group-hover:scale-105"
             loading="lazy" decoding="async">
    </span>

    <span @class(['flex grow flex-col p-6 sm:p-7', 'lg:justify-center lg:p-12' => $featured])>
        <span class="flex flex-wrap items-center gap-3 text-[12px] font-semibold">
            <span class="rounded-full bg-brand-50 px-2.5 py-1 text-brand-600 ring-1 ring-brand-100">{{ $insight['category'] }}</span>
            <span class="flex items-center gap-1.5 text-muted">
                <x-icon name="clock" class="h-3.5 w-3.5" />
                {{ $insight['read_minutes'] }} min read
            </span>
        </span>

        <span @class([
            'mt-4 block font-display font-bold text-ink transition group-hover:text-brand-600',
            'text-2xl leading-tight tracking-[-0.02em] sm:text-[32px]' => $featured,
            'text-xl leading-snug' => ! $featured,
        ])>{{ $insight['title'] }}</span>

        <span @class(['mt-3 block text-[15px] leading-relaxed', 'grow' => ! $featured])>{{ $insight['excerpt'] }}</span>

        <span class="link-arrow mt-6">
            Read guide
            <x-icon name="arrow" class="h-4 w-4 transition group-hover:translate-x-1" />
        </span>
    </span>
</a>
