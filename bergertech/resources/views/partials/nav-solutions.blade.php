<div class="grid grid-cols-[1fr_320px]">
    <div class="p-6">
        <p class="text-[11px] font-bold tracking-[0.16em] text-muted uppercase">Solutions</p>

        <ul class="mt-3 grid grid-cols-2 gap-1">
            @foreach (config('solutions') as $slug => $solution)
                <li>
                    <a href="{{ route('solutions.show', $slug) }}" class="group flex gap-3.5 rounded-xl p-3 transition hover:bg-surface">
                        <span class="h-12 w-16 shrink-0 overflow-hidden rounded-lg">
                            <img src="{{ asset($solution['card_image']) }}" alt=""
                                 class="h-full w-full object-cover transition duration-500 group-hover:scale-110"
                                 loading="lazy" decoding="async">
                        </span>
                        <span class="min-w-0">
                            <span class="block font-display text-[14px] font-bold text-ink">{{ $solution['name'] }}</span>
                            <span class="mt-0.5 line-clamp-2 block text-[12.5px] leading-snug text-muted">{{ $solution['card'] }}</span>
                        </span>
                    </a>
                </li>
            @endforeach

            <li>
                <a href="{{ route('solutions.index') }}" class="group flex h-full items-center gap-3 rounded-xl border border-dashed border-line p-3 transition hover:border-brand-300 hover:bg-brand-50">
                    <span class="flex h-12 w-16 shrink-0 items-center justify-center rounded-lg bg-brand-50 text-brand-600">
                        <x-icon name="arrow" class="h-5 w-5 transition group-hover:translate-x-0.5" />
                    </span>
                    <span class="font-display text-[14px] font-bold text-brand-600">All solutions</span>
                </a>
            </li>
        </ul>
    </div>

    {{-- Featured: the demo offer. --}}
    <div class="relative overflow-hidden p-7 text-white">
        <img src="{{ asset('images/solutions/innovation.jpg') }}" alt=""
             class="absolute inset-0 h-full w-full object-cover" loading="lazy" decoding="async">
        <div class="absolute inset-0 bg-gradient-to-t from-brand-950 via-brand-900/85 to-brand-700/60" aria-hidden="true"></div>

        <div class="relative flex h-full flex-col justify-end">
            <p class="eyebrow eyebrow-dark w-fit">Live demo</p>
            <p class="mt-4 font-display text-xl leading-snug font-bold text-white">See a solution running on real data</p>
            <p class="mt-2 text-[13px] leading-relaxed text-white/75">Tell us the process you want to fix and we will show you what it looks like.</p>
            <a href="{{ route('appointment') }}" class="btn-white mt-5 w-fit px-4 py-2.5 text-[12px]">Get a demo</a>
        </div>
    </div>
</div>
