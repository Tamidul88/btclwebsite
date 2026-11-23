@extends('layouts.app')
@section('title', 'Contact — '.config('site.company'))
@section('description', 'Get in touch with '.config('site.company').'. We reply to every enquiry within one working day.')

@section('content')
    <section class="container-page py-16 lg:py-20">
        <div class="grid gap-14 lg:grid-cols-[1.2fr_1fr]">
            <div>
                <p class="eyebrow">Contact</p>
                <h1 class="mt-3 text-4xl sm:text-5xl">Tell us what you are working on</h1>
                <p class="mt-5 max-w-xl text-lg leading-relaxed">
                    A short description is enough to start. We read every message ourselves and reply within one working day.
                </p>

                <div class="mt-10">
                    <x-enquiry-form type="contact" />
                </div>
            </div>

            <aside class="lg:pt-24">
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-7">
                    <h2 class="text-lg">Direct</h2>
                    <ul class="mt-4 space-y-3 text-sm">
                        <li><a href="mailto:{{ config('site.contact.email') }}" class="font-medium text-brand-700 hover:underline">{{ config('site.contact.email') }}</a></li>
                        <li><a href="tel:{{ preg_replace('/[^0-9+]/', '', config('site.contact.phone')) }}" class="font-medium text-brand-700 hover:underline">{{ config('site.contact.phone') }}</a></li>
                    </ul>

                    <h2 class="mt-7 text-lg">Office</h2>
                    <address class="mt-3 text-sm not-italic leading-relaxed">
                        {{ config('site.contact.address.street') }}<br>
                        {{ config('site.contact.address.locality') }}<br>
                        {{ config('site.contact.address.postal_code') }}
                    </address>
                </div>
            </aside>
        </div>
    </section>
@endsection
