@php
    $groupIcons = [
        'ERP' => 'layers',
        'Cloud' => 'cloud',
        'Apps & CRM' => 'code',
        'Automation' => 'cpu',
        'Security' => 'shield',
    ];
    $tints = ['bg-sky', 'bg-lilac', 'bg-mint', 'bg-blush', 'bg-sand'];
@endphp

<section {{ $attributes->class(['relative overflow-hidden py-20 font-display sm:py-28']) }}>
    <div class="guide-lines pointer-events-none absolute inset-0" aria-hidden="true"></div>

    <div class="container-page relative">
        <x-section-heading
            eyebrow="Technology stack"
            title="Built on proven platforms"
            highlight="proven platforms"
            lead="We are technology-agnostic: we pick the platform that fits the problem, and we know these ones deeply."
            :soft="true"
        />

        {{-- Partner logos --}}
        <ul class="mx-auto mt-12 grid max-w-3xl grid-cols-2 gap-3 sm:grid-cols-4">
            @foreach (config('site.service_cards') as $card)
                <li>
                    <a href="{{ route('services.show', $card['slug']) }}"
                       class="flex h-20 items-center justify-center rounded-2xl border border-line bg-white px-5 transition hover:-translate-y-0.5 hover:border-brand-200 hover:shadow-card">
                        <img src="{{ asset($card['logo']) }}" alt="{{ $card['badge'] }}"
                             class="max-h-11 w-auto max-w-full object-contain" loading="lazy" decoding="async">
                    </a>
                </li>
            @endforeach
        </ul>

        {{-- Platforms by area --}}
        <div class="mt-10 grid gap-4 sm:grid-cols-2 lg:grid-cols-5">
            @foreach (config('site.tech_stack') as $group)
                <div class="reveal card p-6">
                    <p class="flex items-center gap-3 text-[15px] font-semibold text-ink">
                        <span class="leaf-diag flex h-9 w-9 shrink-0 items-center justify-center text-ink {{ $tints[$loop->index % count($tints)] }}">
                            <x-icon :name="$groupIcons[$group['group']] ?? 'sparkle'" class="h-4 w-4" />
                        </span>
                        {{ $group['group'] }}
                    </p>
                    <ul class="mt-4 space-y-2">
                        @foreach ($group['items'] as $item)
                            <li class="flex items-center gap-2 text-[14px] text-ink/80">
                                <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-brand-400"></span>
                                {{ $item }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
        </div>
    </div>
</section>
