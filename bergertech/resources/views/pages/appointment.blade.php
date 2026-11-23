@extends('layouts.app')

@section('title', 'Book for Consultation — '.config('site.short_name'))
@section('description', 'Book a consultation with the Berger Tech Consulting team.')

@php
    $duration = config('site.booking.duration');
    $booked = session('booked');
    $bookedAt = $booked ? app(\App\BookingSlots::class)->parse($booked['at']) : null;
    $slotLabel = fn ($slot) => $slot->format('g:ia').' – '.$slot->addMinutes($duration)->format('g:ia').', '.$slot->format('l, F j, Y');
@endphp

@section('content')

    {{-- No hero: the solid header sits above, so clear its 72px height. --}}
    <section id="book" class="scroll-mt-20 bg-surface pt-[120px] pb-16 sm:pt-[136px] sm:pb-20">
        <div class="container-page">

            @if ($bookedAt)

                {{-- Step 4: confirmation --}}
                <div role="status" class="mx-auto max-w-[640px] overflow-hidden rounded-2xl border border-line bg-white p-8 text-center shadow-card sm:p-12">
                    <div class="bg-brand-panel -mx-8 -mt-8 mb-8 flex justify-center px-8 py-7 sm:-mx-12 sm:-mt-12 sm:mb-10">
                        <img src="{{ asset(config('site.logo')) }}" alt="{{ config('site.name') }}"
                             class="h-11 w-auto" width="1072" height="231" decoding="async">
                    </div>

                    <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-green-600 text-white">
                        <x-icon name="check" class="h-6 w-6" />
                    </span>
                    <h1 class="mt-5 text-2xl font-bold text-ink">You are scheduled</h1>
                    <p class="mt-2 text-[15px]">{{ session('status') }}</p>

                    <div class="mt-8 rounded-xl border border-line p-6 text-left">
                        <p class="font-display text-lg font-bold text-ink">{{ config('site.booking.title') }}</p>
                        <ul class="mt-4 space-y-3 text-[15px] text-slate-600">
                            <li class="flex gap-3"><x-icon name="mail" class="mt-0.5 h-5 w-5 shrink-0 text-slate-400" />{{ $booked['name'] }} · {{ $booked['email'] }}</li>
                            <li class="flex gap-3"><x-icon name="calendar" class="mt-0.5 h-5 w-5 shrink-0 text-slate-400" />{{ $slotLabel($bookedAt) }}</li>
                            <li class="flex gap-3"><x-icon name="globe" class="mt-0.5 h-5 w-5 shrink-0 text-slate-400" />{{ config('site.booking.timezone_label') }}</li>
                            <li class="flex gap-3"><x-icon name="video" class="mt-0.5 h-5 w-5 shrink-0 text-slate-400" />Meeting details will follow by email</li>
                        </ul>
                    </div>

                    <a href="{{ route('appointment') }}#book" class="mt-8 inline-block text-sm font-semibold text-brand-600 hover:underline">Book another time</a>
                </div>

            @else

                <div class="mx-auto max-w-[1060px] overflow-hidden rounded-2xl border border-line bg-white shadow-card lg:grid lg:grid-cols-[320px_1fr]">

                    {{-- Event summary, as in Calendly's left column --}}
                    <aside class="border-b border-line p-6 sm:p-8 lg:border-r lg:border-b-0">
                        {{-- The logo is white artwork, so it sits on the brand gradient. --}}
                        <div class="bg-brand-panel -mx-6 -mt-6 mb-7 flex justify-center px-6 py-7 sm:-mx-8 sm:-mt-8 sm:px-8">
                            <img src="{{ asset(config('site.logo')) }}" alt="{{ config('site.name') }}"
                                 class="h-11 w-auto" width="1072" height="231" decoding="async">
                        </div>

                        <h1 class="text-[26px] leading-tight font-bold text-ink">{{ config('site.booking.title') }}</h1>

                        <ul class="mt-6 space-y-4 text-[15px] font-semibold text-slate-600">
                            <li class="flex gap-3"><x-icon name="clock" class="mt-0.5 h-5 w-5 shrink-0 text-slate-400" />{{ $duration }} min</li>
                            <li class="flex gap-3"><x-icon name="video" class="mt-0.5 h-5 w-5 shrink-0 text-slate-400" />Online or at Berger House, confirmed by email</li>
                            @if ($selected)
                                <li class="flex gap-3"><x-icon name="calendar" class="mt-0.5 h-5 w-5 shrink-0 text-slate-400" />{{ $slotLabel($selected) }}</li>
                                <li class="flex gap-3"><x-icon name="globe" class="mt-0.5 h-5 w-5 shrink-0 text-slate-400" />{{ config('site.booking.timezone_label') }}</li>
                            @endif
                        </ul>

                        <p class="mt-6 text-sm leading-relaxed">
                            Talk to a consultant about SAP, Microsoft Dynamics 365, cloud, automation or security.
                            Tell us what you want to cover and we will come prepared.
                        </p>
                    </aside>

                    <div class="p-6 sm:p-8">

                        @if ($selected)

                            {{-- Step 3: details --}}
                            <a href="{{ route('appointment', ['month' => $month->format('Y-m'), 'date' => $date->toDateString()]) }}#book"
                               class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-line text-brand-600 transition hover:bg-brand-50"
                               aria-label="Back to the calendar">
                                <x-icon name="arrow-left" />
                            </a>

                            <h3 class="mt-5 text-xl font-bold text-ink">Enter Details</h3>

                            @if ($errors->any())
                                <div role="alert" class="mt-5 rounded-lg border border-red-300 bg-red-50 px-5 py-4 text-sm text-red-800">
                                    Please correct the {{ $errors->count() }} highlighted {{ \Illuminate\Support\Str::plural('field', $errors->count()) }} below.
                                </div>
                            @endif

                            <form method="POST" action="{{ route('appointment.store') }}" class="mt-6 grid max-w-[560px] gap-5 sm:grid-cols-2">
                                @csrf
                                <input type="hidden" name="preferred_at" value="{{ $selected->format(\App\BookingSlots::INPUT_FORMAT) }}">

                                <div class="hidden" aria-hidden="true">
                                    <label for="field-website">Website</label>
                                    <input id="field-website" type="text" name="website" value="" tabindex="-1" autocomplete="off">
                                </div>

                                <x-form-field name="name" label="Name" required />
                                <x-form-field name="email" label="Email" type="email" required />
                                <x-form-field name="phone" label="Phone" type="tel" required />
                                <x-form-field name="company" label="Company" />

                                <div class="sm:col-span-2">
                                    <label for="field-topic" class="label">Topic</label>
                                    <select id="field-topic" name="topic" class="field @error('topic') field-error @enderror">
                                        <option value="">Select a topic</option>
                                        @foreach (config('services_content') as $service)
                                            <option value="{{ $service['name'] }}" @selected(old('topic') === $service['name'])>{{ $service['name'] }}</option>
                                        @endforeach
                                        @foreach (config('solutions') as $solution)
                                            <option value="{{ $solution['name'] }}" @selected(old('topic') === $solution['name'])>{{ $solution['name'] }}</option>
                                        @endforeach
                                        <option value="Something else" @selected(old('topic') === 'Something else')>Something else</option>
                                    </select>
                                    @error('topic')
                                        <p class="mt-1.5 text-[13px] text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="sm:col-span-2">
                                    <x-form-field name="notes" label="Please share anything that will help prepare for our meeting." type="textarea" :rows="4" />
                                </div>

                                <div class="sm:col-span-2">
                                    <button type="submit" class="btn-ink px-8">Schedule Event</button>
                                </div>
                            </form>

                        @else

                            {{-- Steps 1 and 2: date, then time --}}
                            <h3 class="text-xl font-bold text-ink">Select a Date &amp; Time</h3>

                            @error('preferred_at')
                                <div role="alert" class="mt-5 rounded-lg border border-red-300 bg-red-50 px-5 py-4 text-sm text-red-800">{{ $message }}</div>
                            @enderror

                            <div @class(['mt-6 grid gap-8', 'md:grid-cols-[1fr_220px]' => $date])>

                                <div @class(['mx-auto w-full max-w-[440px]' => ! $date])>
                                    <div class="flex items-center justify-center gap-6">
                                        @if ($previousMonth)
                                            <a href="{{ route('appointment', ['month' => $previousMonth->format('Y-m')]) }}#book"
                                               class="flex h-10 w-10 items-center justify-center rounded-full bg-brand-50 text-brand-600 transition hover:bg-brand-100"
                                               aria-label="Previous month"><x-icon name="chevron-left" /></a>
                                        @else
                                            <span class="flex h-10 w-10 items-center justify-center text-slate-300" aria-hidden="true"><x-icon name="chevron-left" /></span>
                                        @endif

                                        <p class="w-40 text-center text-[16px] text-ink" aria-live="polite">{{ $month->format('F Y') }}</p>

                                        @if ($nextMonth)
                                            <a href="{{ route('appointment', ['month' => $nextMonth->format('Y-m')]) }}#book"
                                               class="flex h-10 w-10 items-center justify-center rounded-full bg-brand-50 text-brand-600 transition hover:bg-brand-100"
                                               aria-label="Next month"><x-icon name="chevron-right" /></a>
                                        @else
                                            <span class="flex h-10 w-10 items-center justify-center text-slate-300" aria-hidden="true"><x-icon name="chevron-right" /></span>
                                        @endif
                                    </div>

                                    <div class="mt-6 grid grid-cols-7 text-center text-[11px] font-semibold tracking-wider text-slate-500 uppercase">
                                        @foreach (['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $weekday)
                                            <span class="py-2">{{ $weekday }}</span>
                                        @endforeach
                                    </div>

                                    <div class="grid grid-cols-7">
                                        @for ($day = $month->startOfWeek(\Carbon\CarbonInterface::SUNDAY); $day->lessThanOrEqualTo($month->endOfMonth()); $day = $day->addDay())
                                            @php
                                                $isOpen = isset($openDays[$day->toDateString()]);
                                                $isSelected = $date && $date->isSameDay($day);
                                                $isToday = $day->isSameDay($today);
                                            @endphp
                                            <div class="flex justify-center py-1">
                                                @if (! $day->isSameMonth($month))
                                                    <span class="h-11 w-11"></span>
                                                @elseif ($isOpen)
                                                    <a href="{{ route('appointment', ['month' => $month->format('Y-m'), 'date' => $day->toDateString()]) }}#book"
                                                       @class([
                                                           'relative flex h-11 w-11 items-center justify-center rounded-full text-[15px] font-bold transition',
                                                           'bg-brand-600 text-white' => $isSelected,
                                                           'bg-brand-50 text-brand-600 hover:bg-brand-100' => ! $isSelected,
                                                       ])
                                                       aria-label="{{ $day->format('l, F j') }}, times available"
                                                       @if ($isSelected) aria-current="date" @endif>
                                                        {{ $day->day }}
                                                        @if ($isToday)
                                                            <span class="absolute bottom-1.5 h-1 w-1 rounded-full bg-current"></span>
                                                        @endif
                                                    </a>
                                                @else
                                                    <span class="relative flex h-11 w-11 items-center justify-center text-[15px] text-slate-400">
                                                        {{ $day->day }}
                                                        @if ($isToday)
                                                            <span class="absolute bottom-1.5 h-1 w-1 rounded-full bg-current"></span>
                                                        @endif
                                                    </span>
                                                @endif
                                            </div>
                                        @endfor
                                    </div>

                                    @if (! $openDays)
                                        <p class="mt-4 text-center text-sm text-slate-500">No open times this month.
                                            @if ($nextMonth)
                                                <a href="{{ route('appointment', ['month' => $nextMonth->format('Y-m')]) }}#book" class="font-semibold text-brand-600 hover:underline">View next month</a>
                                            @endif
                                        </p>
                                    @endif

                                    <div class="mt-8">
                                        <p class="text-[14px] font-bold text-ink">Time zone</p>
                                        <p class="mt-2 flex items-center gap-2 text-[14px]">
                                            <x-icon name="globe" class="h-4 w-4 text-slate-500" />
                                            {{ config('site.booking.timezone_label') }}
                                        </p>
                                    </div>
                                </div>

                                @if ($date)
                                    <div>
                                        <p class="text-[15px] text-ink">{{ $date->format('l, F j') }}</p>
                                        <ul class="mt-4 max-h-[420px] space-y-2.5 overflow-y-auto pr-1" data-slots>
                                            @foreach ($times as $slot)
                                                @php $slotUrl = route('appointment', ['month' => $month->format('Y-m'), 'date' => $date->toDateString(), 'time' => $slot->format('H:i')]).'#book'; @endphp
                                                <li class="flex gap-2" data-slot>
                                                    <a href="{{ $slotUrl }}" data-slot-time
                                                       class="flex-1 rounded-xl border border-brand-600/40 py-3 text-center text-[15px] font-bold text-brand-600 transition hover:border-brand-600 hover:ring-1 hover:ring-brand-600">
                                                        {{ $slot->format('g:ia') }}
                                                    </a>
                                                    <a href="{{ $slotUrl }}" data-slot-next
                                                       class="hidden flex-1 rounded-xl bg-ink py-3 text-center text-[15px] font-bold text-white transition hover:bg-ink/85">
                                                        Next
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @else
                                    <p class="text-center text-sm text-slate-500">Select a highlighted day to see available times.</p>
                                @endif
                            </div>

                        @endif
                    </div>
                </div>

                <p class="mt-6 text-center text-sm">
                    Prefer to call?
                    <a href="tel:{{ config('site.contact.phone_link') }}" class="font-semibold text-brand-600 hover:underline">{{ config('site.contact.phone') }}</a>
                    · {{ implode(', ', config('site.contact.address_lines')) }}
                </p>

            @endif
        </div>
    </section>

@endsection
