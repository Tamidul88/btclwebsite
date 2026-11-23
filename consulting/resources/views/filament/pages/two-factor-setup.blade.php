<x-filament-panels::page>
    <div class="mx-auto max-w-xl space-y-6">

        @if ($recoveryCodes)
            <div class="rounded-lg border border-warning-300 bg-warning-50 p-5 dark:bg-warning-500/10">
                <h2 class="text-base font-semibold text-warning-800 dark:text-warning-300">
                    Save these recovery codes
                </h2>
                <p class="mt-1 text-sm text-warning-700 dark:text-warning-400">
                    Each code works once. They are shown now and never again.
                </p>
                <ul class="mt-4 grid grid-cols-2 gap-2 font-mono text-sm">
                    @foreach ($recoveryCodes as $recoveryCode)
                        <li class="rounded bg-white px-3 py-2 dark:bg-gray-900">{{ $recoveryCode }}</li>
                    @endforeach
                </ul>
                <a href="{{ route('filament.admin.pages.dashboard') }}"
                   class="mt-5 inline-block text-sm font-semibold text-primary-600">
                    I have saved them — continue
                </a>
            </div>
        @else
            <div class="rounded-lg border border-gray-200 bg-white p-6 dark:border-white/10 dark:bg-gray-900">
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Scan this with your authenticator app, then enter the six-digit code it shows.
                </p>

                <div class="mt-5 flex justify-center rounded-lg bg-white p-4">
                    {{-- Generated server-side by BaconQrCode; safe, self-produced SVG. --}}
                    {!! $qrCode !!}
                </div>

                <details class="mt-4">
                    <summary class="cursor-pointer text-sm text-gray-500">Can't scan it?</summary>
                    <p class="mt-2 break-all font-mono text-xs text-gray-600 dark:text-gray-400">
                        {{ $secret }}
                    </p>
                </details>

                <form wire:submit="confirm" class="mt-6 space-y-4">
                    {{ $this->form }}
                    <x-filament::button type="submit">Confirm and enable</x-filament::button>
                </form>
            </div>
        @endif
    </div>
</x-filament-panels::page>
