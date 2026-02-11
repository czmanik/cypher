<x-filament-panels::page>
    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
        <!-- Summary Widgets -->
        <x-filament::section>
            <x-slot name="heading">
                Celková tržba
            </x-slot>
            <div class="text-3xl font-bold text-success-600 dark:text-success-400">
                {{ number_format($totalRevenue, 0, ',', ' ') }} Kč
            </div>
        </x-filament::section>

        <x-filament::section>
            <x-slot name="heading">
                Počet účtenek
            </x-slot>
            <div class="text-3xl font-bold">
                {{ $billsCount }}
            </div>
        </x-filament::section>

        <!-- Date Display -->
        <x-filament::section>
            <x-slot name="heading">
                Vybrané datum
            </x-slot>
            <div class="text-xl font-bold">
                {{ \Carbon\Carbon::parse($date)->format('d.m.Y') }}
            </div>
        </x-filament::section>
    </div>

    <!-- Simple Table for Data -->
    <x-filament::section>
        <x-slot name="heading">
            Detail účtenek
        </x-slot>

        @if(count($bills) > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-3">ID Účtenky</th>
                            <th scope="col" class="px-6 py-3">Vytvořeno</th>
                            <th scope="col" class="px-6 py-3">Částka (Kč)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($bills as $bill)
                            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                    {{ $bill['billId'] ?? $bill['_id'] ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4">
                                    {{ isset($bill['created']) ? \Carbon\Carbon::parse($bill['created'])->format('H:i:s') : 'N/A' }}
                                </td>
                                <td class="px-6 py-4">
                                    {{ number_format($bill['finalPrice'] ?? $bill['totalAmount'] ?? 0, 0, ',', ' ') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="p-4 text-center text-gray-500">
                Žádná data pro tento den (nebo API vrátilo prázdný seznam).
            </div>
        @endif
    </x-filament::section>
</x-filament-panels::page>
