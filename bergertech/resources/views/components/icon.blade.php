@props(['name'])

@php
    $paths = [
        'pin'      => '<path d="M12 21s-7-6.2-7-11a7 7 0 1 1 14 0c0 4.8-7 11-7 11Z"/><circle cx="12" cy="10" r="2.6"/>',
        'phone'    => '<path d="M6.5 3h3l1.5 4-2 1.4a12 12 0 0 0 5.6 5.6l1.4-2 4 1.5v3a2 2 0 0 1-2.2 2A16.5 16.5 0 0 1 4.5 5.2 2 2 0 0 1 6.5 3Z"/>',
        'mail'     => '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3.5 7 8.5 6 8.5-6"/>',
        'check'    => '<path d="m4 12.5 5 5L20 6.5"/>',
        'arrow'    => '<path d="M5 12h13"/><path d="m12.5 5.5 6.5 6.5-6.5 6.5"/>',
        'calendar' => '<rect x="3" y="5" width="18" height="16" rx="2"/><path d="M8 3v4M16 3v4M3 10h18"/>',
        'clock'    => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5.2l3.2 2"/>',
        'globe'    => '<circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3a14 14 0 0 1 0 18M12 3a14 14 0 0 0 0 18"/>',
        'video'    => '<rect x="3" y="6.5" width="12.5" height="11" rx="2"/><path d="m15.5 10.5 5-3v9l-5-3Z"/>',
        'chevron-left'  => '<path d="m14.5 6-6 6 6 6"/>',
        'chevron-right' => '<path d="m9.5 6 6 6-6 6"/>',
        'arrow-left'    => '<path d="M19 12H6"/><path d="m11.5 5.5-6.5 6.5 6.5 6.5"/>',
        'arrow-up'      => '<path d="M12 19V6"/><path d="m5.5 11.5 6.5-6.5 6.5 6.5"/>',
        'layers'    => '<path d="m12 3 9 5-9 5-9-5 9-5Z"/><path d="m3 13 9 5 9-5"/>',
        'code'      => '<path d="m8.5 7-5 5 5 5M15.5 7l5 5-5 5M13.5 4.5l-3 15"/>',
        'cpu'       => '<rect x="6" y="6" width="12" height="12" rx="2"/><rect x="9.5" y="9.5" width="5" height="5" rx=".5"/><path d="M9 3v3M15 3v3M9 18v3M15 18v3M3 9h3M3 15h3M18 9h3M18 15h3"/>',
        'shield'    => '<path d="M12 3 4.5 6v5.5c0 4.6 3.2 8.1 7.5 9.5 4.3-1.4 7.5-4.9 7.5-9.5V6L12 3Z"/><path d="m9 12 2 2 4-4"/>',
        'clipboard' => '<rect x="5" y="4.5" width="14" height="16.5" rx="2"/><path d="M9 4.5V3h6v1.5M9 11h6M9 15h4"/>',
        'server'    => '<rect x="3.5" y="4" width="17" height="7" rx="1.5"/><rect x="3.5" y="13" width="17" height="7" rx="1.5"/><path d="M7.5 7.5h.01M7.5 16.5h.01"/>',
        'cloud'     => '<path d="M7 18.5a4.5 4.5 0 0 1-.5-9 6 6 0 0 1 11.6 1.6A3.8 3.8 0 0 1 17.5 18.5H7Z"/>',
        'factory'   => '<path d="M3 21V10l5 3V10l5 3V5h4l1 16H3Z"/><path d="M7 17h2M12 17h2"/>',
        'building'  => '<rect x="5" y="3" width="14" height="18" rx="1.5"/><path d="M9 7h1.5M13.5 7H15M9 11h1.5M13.5 11H15M9 15h1.5M13.5 15H15M10.5 21v-3h3v3"/>',
        'bag'       => '<path d="M5.5 8h13l-1 13h-11l-1-13Z"/><path d="M9 10V6.5a3 3 0 0 1 6 0V10"/>',
        'pill'      => '<rect x="3" y="8.5" width="18" height="7" rx="3.5" transform="rotate(-45 12 12)"/><path d="m9.5 9.5 5 5"/>',
        'truck'     => '<path d="M3 6.5h11v10H3zM14 10h4l3 3.5v3h-7"/><circle cx="7" cy="17.5" r="1.8"/><circle cx="17.5" cy="17.5" r="1.8"/>',
        'book'      => '<path d="M4.5 5.5A2 2 0 0 1 6.5 3.5H19v15H6.5a2 2 0 0 0-2 2v-15Z"/><path d="M4.5 20.5a2 2 0 0 1 2-2H19v2.5H6.5"/>',
        'file'      => '<path d="M6 3h8l4 4v14H6V3Z"/><path d="M14 3v4h4M9 12h6M9 16h6"/>',
        'help'      => '<circle cx="12" cy="12" r="9"/><path d="M9.6 9.3a2.5 2.5 0 0 1 4.8.9c0 1.7-2.4 2.2-2.4 3.8M12 17h.01"/>',
        'sparkle'   => '<path d="M12 3.5 13.8 10l6.7 2-6.7 2L12 20.5 10.2 14l-6.7-2 6.7-2L12 3.5Z"/>',
        'target'    => '<circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="5"/><circle cx="12" cy="12" r="1"/>',
        'users'     => '<circle cx="9" cy="8" r="3.5"/><path d="M2.5 20a6.5 6.5 0 0 1 13 0"/><path d="M16 4.6a3.5 3.5 0 0 1 0 6.8M18 14.2a6.5 6.5 0 0 1 3.5 5.8"/>',
        'flag'      => '<path d="M5 21V4"/><path d="M5 4h11l-2 4 2 4H5"/>',
        'plus'          => '<path d="M12 5v14M5 12h14"/>',
        'arrow-up-right' => '<path d="M7 17 17 7"/><path d="M8 7h9v9"/>',
        'linkedin' => '<path d="M6 9v9M6 5.6v.1M11 18v-5a2.5 2.5 0 0 1 5 0v5"/><path d="M11 18v-9"/>',
        'facebook' => '<path d="M14.5 8.5H17V5.2h-2.7c-2.2 0-3.6 1.4-3.6 3.7v1.7H8.4v3.3h2.3V21h3.3v-7.1h2.4l.5-3.3h-2.9V9.4c0-.6.2-.9.5-.9Z"/>',
        'youtube'  => '<rect x="3" y="6" width="18" height="12" rx="3.5"/><path d="m10.5 9.5 5 2.5-5 2.5Z"/>',
    ];
@endphp

<svg {{ $attributes->merge(['class' => 'h-5 w-5']) }}
     viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
     stroke-linecap="round" stroke-linejoin="round"
     aria-hidden="true" focusable="false">
    {!! $paths[$name] ?? $paths['arrow'] !!}
</svg>
