@extends('layouts.app')
@section('title', 'Privacy notice — '.config('site.company'))
@section('robots', 'noindex, follow')

@section('content')
    <section class="container-page max-w-3xl py-16">
        <h1 class="text-4xl">Privacy notice</h1>
        <p class="mt-3 text-sm text-slate-500">Last updated {{ now()->format('F Y') }}</p>

        <div class="mt-10 space-y-8 text-[15px] leading-[1.9]">
            <div>
                <h2 class="text-xl">What we collect</h2>
                <p class="mt-3">When you submit an enquiry we store the name, email address, and any phone number, company name and message you provide, together with any document you attach. We also store a one-way hash of your IP address and your browser's user-agent string.</p>
            </div>

            <div>
                <h2 class="text-xl">Why we store the IP hash</h2>
                <p class="mt-3">Only to detect and block abuse of the form. It is a keyed hash, not a reversible record of your address, and it is deleted on the same schedule as the enquiry itself.</p>
            </div>

            <div>
                <h2 class="text-xl">Attachments</h2>
                <p class="mt-3">Documents you attach are stored outside the public web root and are never served from a public URL. Images are re-encoded on upload, which removes embedded metadata such as EXIF location data. Access is limited to named staff and every download is recorded.</p>
            </div>

            <div>
                <h2 class="text-xl">How long we keep it</h2>
                <p class="mt-3">Enquiries that do not become engagements are deleted after 24 months. If we work together, records are retained for the period required by our professional and tax obligations.</p>
            </div>

            <div>
                <h2 class="text-xl">Your rights</h2>
                <p class="mt-3">You can ask us for a copy of the data we hold about you, ask us to correct it, or ask us to delete it. Write to <a href="mailto:{{ config('site.contact.email') }}" class="font-medium text-brand-700 underline">{{ config('site.contact.email') }}</a> and we will respond within one month.</p>
            </div>

            <div>
                <h2 class="text-xl">Cookies</h2>
                <p class="mt-3">We set one cookie, which holds your session and the CSRF token that protects our forms. It is strictly necessary, carries no analytics identifier, and is removed when you close your browser. See our <a href="{{ route('legal', 'cookies') }}" class="font-medium text-brand-700 underline">cookie notice</a>.</p>
            </div>
        </div>
    </section>
@endsection
