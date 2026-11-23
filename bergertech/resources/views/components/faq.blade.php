@props([
    'items' => config('site.faq'),
])

{{-- Question list on the right, a short prompt with contact routes on the left. --}}
<section id="faq" {{ $attributes->class(['py-20 sm:py-24']) }}>
    <div class="container-page grid gap-12 lg:grid-cols-[1fr_1.5fr] lg:gap-16">

        <div class="lg:sticky lg:top-28 lg:self-start">
            <x-section-heading
                eyebrow="Good to know"
                title="Questions, Answered"
                lead="The things people usually ask before we start working together."
                align="left"
            />

            <div class="mt-8 rounded-2xl bg-surface p-6 ring-1 ring-line">
                <p class="font-display font-semibold text-ink">Still have a question?</p>
                <p class="mt-1.5 text-sm">Talk to a consultant — we reply within one business day.</p>
                <div class="mt-5 flex flex-wrap gap-3">
                    <a href="{{ route('contact') }}" class="btn-mint px-5 py-2.5">Ask us</a>
                    <a href="tel:{{ config('site.contact.phone_link') }}" class="btn-ghost px-5 py-2.5">
                        <x-icon name="phone" class="h-4 w-4" />
                        Call
                    </a>
                </div>
            </div>
        </div>

        <div class="divide-y divide-line border-y border-line">
            @foreach ($items as $item)
                <details class="group py-1" @if ($loop->first) open @endif>
                    <summary class="flex cursor-pointer list-none items-center justify-between gap-6 py-5 [&::-webkit-details-marker]:hidden">
                        <span class="font-display text-[17px] font-semibold text-ink transition group-hover:text-brand-600">{{ $item['question'] }}</span>
                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-brand-50 text-brand-600 transition duration-300 group-open:rotate-45 group-open:bg-brand-600 group-open:text-white">
                            <x-icon name="plus" class="h-4 w-4" />
                        </span>
                    </summary>
                    <p class="max-w-2xl pr-14 pb-6 text-[15px] leading-relaxed">{{ $item['answer'] }}</p>
                </details>
            @endforeach
        </div>
    </div>
</section>
