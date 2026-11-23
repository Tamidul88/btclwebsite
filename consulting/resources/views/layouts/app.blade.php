@php
    $company = config('site.company');
    $metaTitle = trim($__env->yieldContent('title')) ?: $company.' — '.config('site.tagline');
    $metaDescription = trim($__env->yieldContent('description')) ?: config('site.hero.lede');
    $canonical = url()->current();
@endphp
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $metaTitle }}</title>
    <meta name="description" content="{{ \Illuminate\Support\Str::limit($metaDescription, 158) }}">
    <link rel="canonical" href="{{ $canonical }}">
    <meta name="robots" content="@yield('robots', 'index, follow')">

    {{-- Open Graph / Twitter --}}
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:site_name" content="{{ $company }}">
    <meta property="og:title" content="{{ $metaTitle }}">
    <meta property="og:description" content="{{ \Illuminate\Support\Str::limit($metaDescription, 158) }}">
    <meta property="og:url" content="{{ $canonical }}">
    <meta property="og:image" content="{{ asset('images/og-default.svg') }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $metaTitle }}">
    <meta name="twitter:description" content="{{ \Illuminate\Support\Str::limit($metaDescription, 158) }}">
    <meta name="twitter:image" content="{{ asset('images/og-default.svg') }}">

    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Organization schema. Rendered from config, escaped as JSON. --}}
    <script type="application/ld+json" nonce="{{ csp_nonce() }}">
        {!! json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => $company,
            'url' => config('app.url'),
            'description' => config('site.hero.lede'),
            'foundingDate' => (string) config('site.founded'),
            'email' => config('site.contact.email'),
            'telephone' => config('site.contact.phone'),
            'address' => [
                '@type' => 'PostalAddress',
                'streetAddress' => config('site.contact.address.street'),
                'addressLocality' => config('site.contact.address.locality'),
                'addressRegion' => config('site.contact.address.region'),
                'postalCode' => config('site.contact.address.postal_code'),
                'addressCountry' => config('site.contact.address.country'),
            ],
            'sameAs' => array_values(array_filter(config('site.social'))),
        ], JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!}
    </script>

    @stack('head')
</head>
<body class="min-h-screen bg-white font-sans text-slate-600 antialiased">

    <a href="#main" class="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-50 focus:rounded focus:bg-white focus:px-4 focus:py-2 focus:text-ink focus:shadow-lg">
        Skip to content
    </a>

    @include('partials.header')

    <main id="main">
        @yield('content')
    </main>

    @include('partials.footer')

    @stack('scripts')
</body>
</html>
