<x-filament-panels::page>
    <div class="mx-auto max-w-md">
        <div class="rounded-lg border border-gray-200 bg-white p-6 dark:border-white/10 dark:bg-gray-900">
            <p class="text-sm text-gray-600 dark:text-gray-400">
                @if ($useRecoveryCode)
                    Enter one of your single-use recovery codes.
                @else
                    Enter the six-digit code from your authenticator app.
                @endif
            </p>

            <form wire:submit="submit" class="mt-5 space-y-4">
                {{ $this->form }}
                <x-filament::button type="submit" class="w-full">Verify</x-filament::button>
            </form>

            <button type="button" wire:click="toggleRecovery"
                    class="mt-4 text-sm text-primary-600 hover:underline">
                {{ $useRecoveryCode ? 'Use an authenticator code instead' : 'Use a recovery code instead' }}
            </button>
        </div>
    </div>
</x-filament-panels::page>
