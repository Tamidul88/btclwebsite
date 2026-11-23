@props([
    'heading' => 'Get in Touch for Solution Demo',
    'text' => 'Tell us what you want to fix and we will show you the solution running on real data.',
    'cta' => 'Set an Appointment',
])

<section class="bg-white py-20 sm:py-24">
    <div class="container-page">
        <div class="bg-brand-band relative overflow-hidden rounded-2xl px-6 py-12 text-white shadow-float sm:px-12 sm:py-14 lg:px-16">

            <div class="dot-grid pointer-events-none absolute inset-0" aria-hidden="true"></div>
            <div class="pointer-events-none absolute -top-24 -right-16 h-72 w-72 rounded-full bg-brand-400/40 blur-3xl" aria-hidden="true"></div>

            <div class="relative flex flex-col items-center gap-8 text-center lg:flex-row lg:justify-between lg:text-left">
                <div class="max-w-xl">
                    <h2 class="font-display text-2xl leading-tight font-bold text-white sm:text-[34px]">{{ $heading }}</h2>
                    <p class="mt-4 text-[15px] leading-relaxed text-white/80">{{ $text }}</p>
                </div>

                <div class="flex shrink-0 flex-wrap justify-center gap-3">
                    <a href="{{ route('appointment') }}" class="btn-white group">
                        <x-icon name="calendar" class="h-4 w-4" />
                        {{ $cta }}
                    </a>
                    <a href="{{ route('contact') }}" class="btn-outline">Contact Us</a>
                </div>
            </div>
        </div>
    </div>
</section>
