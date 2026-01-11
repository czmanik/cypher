<x-filament-panels::page>
    <div class="space-y-6">
        @if($this->getTable()->getQuery()->count() === 0)
            <div class="flex flex-col items-center justify-center p-6 text-gray-500 bg-white rounded-lg shadow dark:bg-gray-800">
                <x-heroicon-o-check-circle class="w-12 h-12 text-gray-400 mb-2"/>
                <p class="text-lg font-medium">Žádné volné směny</p>
                <p class="text-sm">Momentálně nejsou k dispozici žádné volné směny pro vaši pozici.</p>
            </div>
        @endif

        {{ $this->table }}
    </div>
</x-filament-panels::page>
