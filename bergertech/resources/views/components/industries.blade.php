@php
    $tints = ['bg-sky', 'bg-lilac', 'bg-mint', 'bg-blush', 'bg-sand'];
    $customerCount = floor(count(config('site.customers')) / 10) * 10;
@endphp

{{-- overflow-clip, not hidden: a scroll container would break the sticky intro column. --}}
<section {{ $attributes->class(['relative overflow-clip py-20 font-display sm:py-28']) }}>
    <div class="guide-lines pointer-events-none absolute inset-0" aria-hidden="true"></div>

    <div class="container-page relative">
        <div class="grid gap-12 lg:grid-cols-[1fr_1.8fr] lg:gap-16">
            <div class="lg:sticky lg:top-28 lg:self-start">
                <x-section-heading
                    eyebrow="Industries"
                    title="Trusted across industries"
                    highlight="industries"
                    lead="From steel and cement to pharma and consumer brands, we understand the processes that keep each sector running."
                    align="left"
                    :soft="true"
                />

                {{-- Figures on pastel tiles with one swept corner. --}}
                <dl class="mt-10 grid grid-cols-2 gap-4">
                    <div class="leaf-tr flex flex-col-reverse bg-sky p-5">
                        <dt class="mt-1 text-[13px] font-medium text-ink/70">Industries served</dt>
                        <dd class="text-4xl font-semibold text-sky-ink" data-count="{{ count(config('site.industries')) }}">{{ count(config('site.industries')) }}</dd>
                    </div>
                    <div class="leaf-tr flex flex-col-reverse bg-lilac p-5">
                        <dt class="mt-1 text-[13px] font-medium text-ink/70">Enterprise customers</dt>
                        <dd class="text-4xl font-semibold text-lilac-ink" data-count="{{ $customerCount }}" data-suffix="+">{{ $customerCount }}+</dd>
                    </div>
                </dl>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                @foreach (config('site.industries') as $industry)
                    <div class="reveal card card-hover group p-6">
                        <div class="flex items-center gap-4">
                            <span class="leaf-diag flex h-12 w-12 shrink-0 items-center justify-center text-ink {{ $tints[$loop->index % count($tints)] }}">
                                <x-icon :name="$industry['icon']" class="h-6 w-6" />
                            </span>
                            <h3 class="text-lg text-ink">{{ $industry['name'] }}</h3>
                        </div>
                        <ul class="mt-5 flex flex-wrap gap-1.5">
                            @foreach ($industry['customers'] as $customer)
                                <li class="rounded-full bg-surface px-3 py-1 text-[12.5px] text-ink/75">{{ $customer }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
