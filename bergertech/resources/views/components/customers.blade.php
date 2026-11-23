@props([
    'customers' => config('site.customers'),
])

{{--
    Customer logos in one white panel on the streaked background. The list is a
    native scroller, so it swipes on touch; resources/js/customers.js adds the
    arrows, the auto-advance and the endless loop.
--}}
<section {{ $attributes->class(['bg-diagonal-streaks py-20 sm:py-24']) }}>
    <div class="container-page">
        <x-section-heading eyebrow="Trusted by industry leaders" title="Our Customers" />

        <div data-customers class="mt-12 flex items-center rounded-2xl bg-white px-1 py-8 shadow-card sm:px-3 sm:py-12" role="group" aria-label="Our customers">
            <button type="button" data-customers-prev aria-label="Previous customers"
                    class="flex h-11 w-9 shrink-0 items-center justify-center rounded-md text-ink/35 transition hover:text-ink sm:w-11">
                <x-icon name="chevron-left" class="h-6 w-6" />
            </button>

            <ul data-customers-list
                class="relative flex min-w-0 grow snap-x snap-mandatory overflow-x-auto [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
                {{-- Listed twice so the loop is seamless; the copy is hidden from screen readers. --}}
                @foreach ([false, true] as $isCopy)
                    @foreach ($customers as $customer)
                        <li class="flex h-28 shrink-0 basis-1/2 snap-start items-center justify-center px-3 sm:basis-1/4 lg:basis-[calc(100%/7)]"
                            @if ($isCopy) aria-hidden="true" @endif>
                            <img src="{{ asset($customer['logo']) }}" alt="{{ $isCopy ? '' : $customer['name'] }}"
                                 class="h-full w-full max-w-[124px] object-contain" loading="lazy" decoding="async">
                        </li>
                    @endforeach
                @endforeach
            </ul>

            <button type="button" data-customers-next aria-label="Next customers"
                    class="flex h-11 w-9 shrink-0 items-center justify-center rounded-md text-ink/35 transition hover:text-ink sm:w-11">
                <x-icon name="chevron-right" class="h-6 w-6" />
            </button>
        </div>
    </div>
</section>
