@extends('layouts.app', ['transparentHeader' => true])

@section('title', 'Contact Us — '.config('site.short_name'))
@section('description', 'Contact Berger Tech Consulting Limited at Berger House, Uttara Model Town, Dhaka 1230.')

@section('content')

    <x-hero
        eyebrow="Contact"
        title="To Connect With Us"
        highlight="Connect"
        subtitle="Tell us what you are trying to solve. We will point you at the shortest route."
        image="images/services/cloud-team.jpg"
        :breadcrumbs="[['label' => 'Home', 'url' => route('home')], ['label' => 'Contact']]"
        :eager="true"
    />

    <section class="bg-surface py-20 sm:py-24">
        <div class="container-page grid items-start gap-8 lg:grid-cols-[1.15fr_1fr] lg:gap-10">

            {{-- Form --}}
            <div class="card p-6 sm:p-10">
                <p class="eyebrow">Write to us</p>
                <h2 class="mt-3 font-display text-2xl font-bold text-ink sm:text-3xl">Send us a message</h2>
                <p class="mt-3 text-[15px]">We reply to every message within one business day.</p>

                @if (session('status'))
                    <div role="status" class="mt-6 rounded-lg border border-green-300 bg-green-50 px-5 py-4 text-sm text-green-800">
                        {{ session('status') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div role="alert" class="mt-6 rounded-lg border border-red-300 bg-red-50 px-5 py-4 text-sm text-red-800">
                        Please correct the {{ $errors->count() }} highlighted {{ \Illuminate\Support\Str::plural('field', $errors->count()) }} below.
                    </div>
                @endif

                <form method="POST" action="{{ route('contact.store') }}" class="mt-8 grid gap-5 sm:grid-cols-2">
                    @csrf

                    {{-- Honeypot; kept out of the tab order and off screen. --}}
                    <div class="hidden" aria-hidden="true">
                        <label for="field-website">Website</label>
                        <input id="field-website" type="text" name="website" value="" tabindex="-1" autocomplete="off">
                    </div>

                    <x-form-field name="name" label="Name" required />
                    <x-form-field name="email" label="Email" type="email" required />
                    <x-form-field name="phone" label="Phone" type="tel" />
                    <x-form-field name="company" label="Company" />

                    <div class="sm:col-span-2">
                        <x-form-field name="subject" label="Subject" placeholder="SAP, Dynamics 365, cloud, a solution demo…" />
                    </div>

                    <div class="sm:col-span-2">
                        <x-form-field name="message" label="Message" type="textarea" required />
                    </div>

                    <div class="sm:col-span-2">
                        <button type="submit" class="btn-ink group w-full sm:w-auto">
                            Send Message
                            <x-icon name="arrow" class="h-4 w-4 transition group-hover:translate-x-0.5" />
                        </button>
                    </div>
                </form>
            </div>

            {{-- Details --}}
            <aside class="space-y-6 lg:sticky lg:top-28">
                <div class="bg-brand-panel relative overflow-hidden rounded-2xl p-8 text-white shadow-float sm:p-10">
                    <div class="dot-grid pointer-events-none absolute inset-0" aria-hidden="true"></div>
                    <div class="relative">
                        <h2 class="font-display text-xl font-bold text-white">Berger Tech Consulting Ltd.</h2>

                        <ul class="mt-6 space-y-4 text-sm">
                            <li class="flex gap-3">
                                <x-icon name="pin" class="mt-0.5 h-5 w-5 shrink-0 text-brand-200" />
                                <span class="text-white/85">{{ implode(', ', config('site.contact.address_lines')) }}</span>
                            </li>
                            <li class="flex gap-3">
                                <x-icon name="phone" class="mt-0.5 h-5 w-5 shrink-0 text-brand-200" />
                                <a href="tel:{{ config('site.contact.phone_link') }}" class="text-white/85 hover:text-white">{{ config('site.contact.phone') }}</a>
                            </li>
                            <li class="flex gap-3">
                                <x-icon name="mail" class="mt-0.5 h-5 w-5 shrink-0 text-brand-200" />
                                <a href="mailto:{{ config('site.contact.email') }}" class="text-white/85 hover:text-white">{{ config('site.contact.email') }}</a>
                            </li>
                        </ul>

                        <div class="mt-8 flex flex-wrap gap-3">
                            <a href="{{ config('site.contact.map') }}" target="_blank" rel="noopener noreferrer" class="btn-outline">Google map</a>
                            <a href="{{ route('appointment') }}" class="btn-white">Book for Consultation</a>
                        </div>
                    </div>
                </div>

                <div class="card flex gap-4 p-6">
                    <span class="icon-tile h-11 w-11"><x-icon name="clock" /></span>
                    <div>
                        <h3 class="font-display text-base font-bold text-ink">Office hours</h3>
                        <p class="mt-1.5 text-sm">Sunday to Thursday, 9:00 AM – 6:00 PM (GMT+6). We reply to messages within one business day.</p>
                    </div>
                </div>
            </aside>
        </div>
    </section>

    <x-faq class="bg-white" />

@endsection
