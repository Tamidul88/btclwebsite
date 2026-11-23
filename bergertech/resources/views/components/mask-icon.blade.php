@props(['src'])

@inject('pageStyles', 'App\PageStyles')

{{--
    Paints a PNG icon in the current text colour by using it as a mask.
    The "why us" icon set mixes black and white artwork; masking makes every
    icon one consistent colour that can also change on hover.
--}}
@php
    $mask = "url('".asset($src)."') center / contain no-repeat";
    $maskClass = $pageStyles->classFor("-webkit-mask: {$mask}; mask: {$mask};");
@endphp
<span {{ $attributes->merge(['class' => 'block shrink-0 bg-current '.$maskClass]) }}
      aria-hidden="true"></span>
