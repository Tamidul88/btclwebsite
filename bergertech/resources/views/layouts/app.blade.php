<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('site.name'))</title>
    <meta name="description" content="@yield('description', 'Berger Tech Consulting Limited is a next-generation technology solutions and services company simplifying business through SAP, Microsoft Dynamics 365, cloud and automation.')">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">

    {{-- Lets the Vite dev client put the CSP nonce on the styles it injects. --}}
    <meta property="csp-nonce" nonce="{{ Vite::cspNonce() }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Per-page values (accent colours, icon masks) collected by App\PageStyles. --}}
    @inject('pageStyles', 'App\PageStyles')
    @if ($pageStyles->css() !== '')
        <style nonce="{{ Vite::cspNonce() }}">{!! $pageStyles->css() !!}</style>
    @endif
</head>
<body id="top" class="flex min-h-screen flex-col overflow-x-hidden">

    <a href="#main" class="sr-only focus:not-sr-only focus:fixed focus:top-3 focus:left-3 focus:z-[100] focus:rounded focus:bg-white focus:px-4 focus:py-2 focus:text-brand-700">
        Skip to content
    </a>

    @include('partials.header')

    <main id="main" class="grow">
        @yield('content')
    </main>

    @include('partials.footer')

    {{-- Fixed "Flip book" tab, bottom-left, exactly as on the source site. --}}
    <a href="{{ route('services.index') }}"
       class="fixed bottom-6 left-0 z-40 hidden -rotate-90 origin-bottom-left translate-y-[-1rem] rounded-t-md bg-[#e8622a] px-5 py-2 font-display text-[13px] font-bold tracking-wide text-white shadow-float transition hover:bg-[#d4541f] sm:block">
        Flip book
    </a>

</body>
</html>
