@extends('layouts.app')
@section('title', 'Something went wrong — '.config('site.company'))
@section('robots', 'noindex, nofollow')

@section('content')
    <section class="container-page flex min-h-[55vh] max-w-xl flex-col justify-center py-20 text-center">
        <p class="text-sm font-semibold tracking-[0.2em] text-brand-600">500</p>
        <h1 class="mt-4 text-4xl">Something went wrong</h1>
        {{-- Deliberately generic: error pages must never echo request data or
             internal detail, in any environment. --}}
        <p class="mt-4 text-[15px] leading-relaxed">We have been notified and are looking into it.</p>
        <div class="mt-8 flex justify-center gap-3">
            <a href="{{ route('home') }}" class="btn-primary">Back to the homepage</a>
            <a href="{{ route('contact') }}" class="btn-secondary">Contact us</a>
        </div>
    </section>
@endsection
