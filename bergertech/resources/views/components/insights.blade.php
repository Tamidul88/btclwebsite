<section id="insights" {{ $attributes->class(['py-20 sm:py-28']) }}>
    <div class="container-page">
        <div class="flex flex-col gap-6 md:flex-row md:items-end md:justify-between">
            <x-section-heading
                eyebrow="Insights & resources"
                title="Ideas worth reading"
                highlight="worth reading"
                lead="Our thinking on digital transformation, cloud migration and where Berger Tech is heading next."
                align="left"
            />
            <a href="{{ route('insights.index') }}" class="link-arrow shrink-0">
                All insights
                <x-icon name="arrow" class="h-4 w-4" />
            </a>
        </div>

        <div class="mt-12 grid gap-6 md:grid-cols-2">
            @foreach (config('site.features') as $feature)
                <article class="reveal group relative flex min-h-[340px] flex-col justify-end overflow-hidden rounded-2xl p-8 text-white shadow-card sm:p-10">
                    <img src="{{ asset($feature['image']) }}" alt=""
                         class="absolute inset-0 h-full w-full object-cover transition duration-700 group-hover:scale-105"
                         loading="lazy" decoding="async" aria-hidden="true">
                    <div class="absolute inset-0 bg-gradient-to-t from-brand-950/95 via-brand-950/60 to-brand-950/10" aria-hidden="true"></div>

                    <div class="relative">
                        <span class="block h-1 w-10 rounded-full bg-brand-400"></span>
                        <h3 class="mt-5 font-display text-2xl leading-tight font-bold text-white sm:text-3xl">
                            {{ $feature['kicker'] }}
                        </h3>
                        <p class="mt-3 max-w-md text-[15px] text-white/85">{{ $feature['title'] }}</p>
                        <a href="{{ isset($feature['insight']) ? route('insights.show', $feature['insight']) : route($feature['url_name']) }}" class="btn-outline mt-7">
                            {{ $feature['cta'] }}
                            <x-icon name="arrow" class="h-4 w-4" />
                        </a>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>
