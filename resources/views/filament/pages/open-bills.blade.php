<x-filament-panels::page>
    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
        <x-filament::section>
            <x-slot name="heading">
                Celková částka (Otevřené)
            </x-slot>
            <div class="text-3xl font-bold text-warning-600">
                {{ number_format($totalAmount, 0, ',', ' ') }} Kč
            </div>
            <div class="text-sm text-gray-500 mt-2">
                Aktuálně otevřené stoly (mimo personální)
            </div>
        </x-filament::section>

        <x-filament::section>
            <x-slot name="heading">
                Počet otevřených účtů
            </x-slot>
            <div class="text-3xl font-bold">
                {{ count($openBills) }}
            </div>
        </x-filament::section>
    </div>

    <x-filament::section>
        <x-slot name="heading">
            Seznam otevřených účtů
        </x-slot>

        @if(count($openBills) > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th class="px-6 py-3">Stůl / ID</th>
                            <th class="px-6 py-3">Otevřel</th>
                            <th class="px-6 py-3">Kategorie</th>
                            <th class="px-6 py-3 text-right">Částka</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($openBills as $bill)
                            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                <td class="px-6 py-4 font-medium">
                                    {{ $bill['tableName'] ?? $bill['tableId'] ?? $bill['_id'] ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4">
                                    {{ $bill['openedBy']['fullName'] ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4">
                                    {{ $bill['categoryName'] ?? '-' }}
                                </td>
                                <td class="px-6 py-4 text-right font-bold">
                                    {{ number_format((float)($bill['totalAmount'] ?? $bill['finalPrice'] ?? 0), 0, ',', ' ') }} Kč
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="p-4 text-center text-gray-500">
                Žádné otevřené účty.
            </div>
        @endif
    </x-filament::section>
</x-filament-panels::page>
