@extends('layouts.app')
@section('title', 'Terms of engagement — '.config('site.company'))
@section('robots', 'noindex, follow')

@section('content')
    <section class="container-page max-w-3xl py-16">
        <h1 class="text-4xl">Terms of engagement</h1>
        <p class="mt-3 text-sm text-slate-500">Last updated {{ now()->format('F Y') }}</p>
        <div class="mt-10 space-y-5 text-[15px] leading-[1.9]">
            @yield('legal_body')
        </div>
    </section>
@endsection
