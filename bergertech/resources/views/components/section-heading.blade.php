@props([
    'title',
    'eyebrow' => null,
    'lead' => null,
    'highlight' => null,
    'align' => 'center',
    'dark' => false,
    'soft' => false,
    'tag' => 'h2',
])

@php
    $centered = $align === 'center';

    // Escape first, then wrap the highlighted phrase in the accent span:
    // a gradient by default, solid brand blue in the soft variant.
    $heading = e($title);
    if ($highlight) {
        $highlightClass = $soft ? 'text-brand-600' : 'text-gradient';
        $heading = str_replace(e($highlight), '<span class="'.$highlightClass.'">'.e($highlight).'</span>', $heading);
    }
@endphp

<div {{ $attributes->class(['max-w-3xl', 'mx-auto text-center' => $centered]) }}>
    {{-- Eyebrow labels are switched off site-wide. Uncomment this block and the mt-5 entry below to bring them back.
    @if ($eyebrow)
        <p @class(['eyebrow', 'eyebrow-dark' => $dark, 'eyebrow-soft' => $soft])>{{ $eyebrow }}</p>
    @endif
    --}}

    <{{ $tag }} @class([
        'section-title',
        'section-title-soft' => $soft,
        {{-- 'mt-5' => $eyebrow, --}}
        'text-white' => $dark,
    ])>{!! $heading !!}</{{ $tag }}>

    @if ($lead)
        <p @class(['section-lead mt-5', 'mx-auto max-w-2xl' => $centered, 'text-white/80' => $dark])>{{ $lead }}</p>
    @endif
</div>
