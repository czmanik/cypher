<x-filament-panels::page>
    <x-filament::section>
        <div class="max-w-md mx-auto text-center">
            <p class="mb-4 text-gray-500">Zde můžete načíst QR kódy voucherů nebo produktů.</p>

            <form wire:submit.prevent="submitCode" class="space-y-4">
                {{ $this->form }}

                <x-filament::button type="submit" size="lg" class="w-full">
                    Ověřit kód
                </x-filament::button>
            </form>
        </div>
    </x-filament::section>
</x-filament-panels::page>
