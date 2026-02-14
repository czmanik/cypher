<x-filament-panels::page>
    <!-- Date Navigation -->
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4 bg-white dark:bg-gray-900 p-4 rounded-lg shadow mb-4 border dark:border-gray-700">
        <div class="flex items-center gap-4">
             <x-filament::button
                color="gray"
                icon="heroicon-o-chevron-left"
                wire:click="previousDay"
                size="sm"
            >
                Předchozí
            </x-filament::button>

            <div class="text-xl font-bold min-w-[120px] text-center">
                {{ \Carbon\Carbon::parse($date)->format('d.m.Y') }}
            </div>

            <x-filament::button
                color="gray"
                icon="heroicon-o-chevron-right"
                icon-position="after"
                wire:click="nextDay"
                size="sm"
            >
                Následující
            </x-filament::button>
        </div>

        <!-- Custom Actions (Select Date / Refresh) -->
        <!-- Since we are in a custom view, standard header actions render separately.
             But we can access them if we wanted, or just rely on the Page Header.
             The user request implies these controls should be here.
             We'll keep the standard page header actions for "Select Date" and "Refresh" as they are robust.
             However, the request says "Storyous Přehled < Previous [Date] Next > Change Date | Refresh".
             Let's try to mimic that flow or keep the Page Header actions.
             Filament renders page header actions at the top right automatically.
             So we have Title on left, Actions on right.
             The Date Nav is best placed just below the title area or as a custom widget.
        -->
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
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

        <x-filament::section>
            <x-slot name="heading">
                Počet hostů
            </x-slot>
            <div class="text-3xl font-bold text-primary-600 dark:text-primary-400">
                {{ $totalGuests > 0 ? $totalGuests : 'N/A' }}
            </div>
        </x-filament::section>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
        <!-- Revenue Chart -->
        <x-filament::section>
            @livewire(\App\Filament\Widgets\StoryousCategoryRevenueChart::class, ['date' => $date])
        </x-filament::section>

        <!-- Sold Items Summary -->
        <x-filament::section>
            <x-slot name="heading">
                Přehled prodaných položek
            </x-slot>

            @if(count($soldItems) > 0)
                <div class="space-y-4 max-h-[400px] overflow-y-auto">
                    @foreach($soldItems as $categoryGroup)
                        <div
                            x-data="{ open: false }"
                            class="border rounded-lg overflow-hidden dark:border-gray-700"
                        >
                            <!-- Category Header -->
                            <button
                                @click="open = !open"
                                class="w-full flex items-center justify-between p-3 bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 transition"
                            >
                                <div class="flex items-center gap-2">
                                    <x-heroicon-o-chevron-down x-show="open" class="w-4 h-4" />
                                    <x-heroicon-o-chevron-right x-show="!open" class="w-4 h-4" />
                                    <span class="font-bold text-lg">{{ $categoryGroup['category_name'] }}</span>
                                </div>
                                <div class="text-sm font-semibold text-gray-600 dark:text-gray-300">
                                    {{ number_format($categoryGroup['total_revenue'], 0, ',', ' ') }} Kč
                                </div>
                            </button>

                            <!-- Items Grid -->
                            <div x-show="open" class="p-4 bg-white dark:bg-gray-900 border-t dark:border-gray-700">
                                <div class="grid grid-cols-1 gap-2">
                                    @foreach($categoryGroup['items'] as $item)
                                        <div class="flex justify-between items-center bg-gray-50 dark:bg-gray-800 p-2 rounded border dark:border-gray-700 text-sm">
                                            <div class="flex flex-col overflow-hidden">
                                                <span class="font-medium truncate" title="{{ $item['name'] }}">{{ $item['name'] }}</span>
                                                <span class="text-xs text-gray-500">{{ number_format($item['revenue'], 0, ',', ' ') }} Kč</span>
                                            </div>
                                            <span class="font-bold text-primary-600 dark:text-primary-400 whitespace-nowrap ml-2">
                                                {{ $item['count'] }}x
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-gray-500 italic">Žádné položky k zobrazení.</div>
            @endif
        </x-filament::section>
    </div>

    <!-- Simple Table for Data -->
            <div class="space-y-4">
                @foreach($soldItems as $categoryGroup)
                    <div
                        x-data="{ open: false }"
                        class="border rounded-lg overflow-hidden dark:border-gray-700"
                    >
                        <!-- Category Header -->
                        <button
                            @click="open = !open"
                            class="w-full flex items-center justify-between p-3 bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 transition"
                        >
                            <div class="flex items-center gap-2">
                                <x-heroicon-o-chevron-down x-show="open" class="w-4 h-4" />
                                <x-heroicon-o-chevron-right x-show="!open" class="w-4 h-4" />
                                <span class="font-bold text-lg">{{ $categoryGroup['category_name'] }}</span>
                            </div>
                            <div class="text-sm font-semibold text-gray-600 dark:text-gray-300">
                                {{ number_format($categoryGroup['total_revenue'], 0, ',', ' ') }} Kč
                            </div>
                        </button>

                        <!-- Items Grid -->
                        <div x-show="open" class="p-4 bg-white dark:bg-gray-900 border-t dark:border-gray-700">
                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2">
                                @foreach($categoryGroup['items'] as $item)
                                    <div class="flex justify-between items-center bg-gray-50 dark:bg-gray-800 p-2 rounded border dark:border-gray-700 text-sm">
                                        <div class="flex flex-col overflow-hidden">
                                            <span class="font-medium truncate" title="{{ $item['name'] }}">{{ $item['name'] }}</span>
                                            <span class="text-xs text-gray-500">{{ number_format($item['revenue'], 0, ',', ' ') }} Kč</span>
                                        </div>
                                        <span class="font-bold text-primary-600 dark:text-primary-400 whitespace-nowrap ml-2">
                                            {{ $item['count'] }}x
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-gray-500 italic">Žádné položky k zobrazení.</div>
        @endif
    </x-filament::section>

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
                            <th scope="col" class="px-6 py-3">Čas</th>
                            <th scope="col" class="px-6 py-3">ID Účtenky</th>
                            <th scope="col" class="px-6 py-3">Hostů</th>
                            <th scope="col" class="px-6 py-3">Spropitné</th>
                            <th scope="col" class="px-6 py-3">Sleva</th>
                            <th scope="col" class="px-6 py-3">Částka (Kč)</th>
                            <th scope="col" class="px-6 py-3">Akce</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($bills as $bill)
                            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                <td class="px-6 py-4">
                                    {{ isset($bill['created']) ? \Carbon\Carbon::parse($bill['created'])->format('H:i:s') : 'N/A' }}
                                </td>
                                <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                    {{ $bill['billId'] ?? $bill['_id'] ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4">
                                    {{ $bill['personCount'] ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 text-success-600">
                                    {{ isset($bill['tips']) ? number_format((float)$bill['tips'], 0, ',', ' ') . ' Kč' : '-' }}
                                </td>
                                <td class="px-6 py-4 text-danger-600">
                                    @if(isset($bill['discount']) && (float)$bill['discount'] > 0)
                                        {{ number_format((float)$bill['discount'], 0, ',', ' ') . ' Kč' }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-6 py-4 font-bold">
                                    {{ number_format($bill['finalPrice'] ?? $bill['totalAmount'] ?? 0, 0, ',', ' ') }}
                                </td>
                                <td class="px-6 py-4">
                                    <x-filament::button
                                        size="xs"
                                        color="gray"
                                        wire:click="openBillDetail('{{ $bill['billId'] ?? '' }}')"
                                    >
                                        Detail
                                    </x-filament::button>
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

    <!-- Detail Modal -->
    <x-filament::modal id="bill-detail-modal" width="3xl">
        <x-slot name="heading">
            Detail účtenky
        </x-slot>

        @if($selectedBill)
            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <span class="font-bold">ID:</span> {{ $selectedBill['billId'] ?? '-' }}
                    </div>
                    <div>
                        <span class="font-bold">Čas:</span> {{ isset($selectedBill['created']) ? \Carbon\Carbon::parse($selectedBill['created'])->format('d.m.Y H:i:s') : '-' }}
                    </div>
                    <div>
                        <span class="font-bold">Obsluha (Otevřel):</span> {{ $selectedBill['createdBy']['fullName'] ?? '-' }}
                    </div>
                     <div>
                        <span class="font-bold">Obsluha (Zavřel):</span> {{ $selectedBill['paidBy']['fullName'] ?? '-' }}
                    </div>
                    <div>
                        <span class="font-bold">Způsob platby:</span> {{ $selectedBill['paymentMethod'] ?? '-' }}
                    </div>
                </div>

                <hr class="border-gray-200 dark:border-gray-700">

                <div>
                    <h3 class="font-bold text-lg mb-2">Položky a Platby</h3>

                    @if(isset($selectedBill['items']) && is_array($selectedBill['items']))
                        <div class="mb-4">
                            <h4 class="font-semibold mb-2">Objednané položky</h4>
                            <table class="w-full text-sm text-left">
                                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                    <tr>
                                        <th class="px-2 py-1">Název</th>
                                        <th class="px-2 py-1 text-right">Množství</th>
                                        <th class="px-2 py-1 text-right">Cena/ks</th>
                                        <th class="px-2 py-1 text-right">Celkem</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($selectedBill['items'] as $item)
                                        <tr class="border-b dark:border-gray-700">
                                            <td class="px-2 py-1">{{ $item['name'] ?? 'Neznámá položka' }}</td>
                                            <td class="px-2 py-1 text-right">{{ $item['amount'] ?? 1 }} {{ $item['measure'] ?? 'ks' }}</td>
                                            <td class="px-2 py-1 text-right">{{ number_format((float)($item['unitPriceWithVat'] ?? $item['price'] ?? 0), 2) }}</td>
                                            <td class="px-2 py-1 text-right font-semibold">{{ number_format((float)($item['priceWithVat'] ?? 0), 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif

                    @if(isset($selectedBill['payments']) && is_array($selectedBill['payments']))
                         <h4 class="font-semibold mt-2">Platby</h4>
                         <ul class="list-disc pl-5">
                            @foreach($selectedBill['payments'] as $payment)
                                <li>
                                    {{ $payment['paymentMethod'] ?? 'Unknown' }}:
                                    {{ number_format($payment['amount'] ?? $payment['priceWithVat'] ?? 0, 2) }} {{ $selectedBill['currencyCode'] ?? 'CZK' }}
                                </li>
                            @endforeach
                         </ul>
                    @endif
                </div>

                <div class="bg-gray-50 dark:bg-gray-800 p-4 rounded text-right">
                    <div>Spropitné: <span class="font-bold">{{ number_format((float)($selectedBill['tips'] ?? 0), 2) }}</span></div>
                    <div>Sleva: <span class="font-bold text-red-500">{{ number_format((float)($selectedBill['discount'] ?? 0), 2) }}</span></div>
                    <div class="text-xl mt-2">Celkem: <span class="font-bold text-primary-600">{{ number_format($selectedBill['finalPrice'] ?? 0, 2) }} {{ $selectedBill['currencyCode'] ?? 'CZK' }}</span></div>
                </div>

                <!-- Raw Data Dump for Debugging (Optional, maybe hidden or collapsed) -->
                <details>
                    <summary class="cursor-pointer text-xs text-gray-400 mt-4">Zobrazit raw data</summary>
                    <pre class="text-xs overflow-auto max-h-40 bg-gray-100 dark:bg-gray-900 p-2 rounded mt-2">{{ json_encode($selectedBill, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                </details>
            </div>
        @else
            <div class="text-center py-4">Načítání...</div>
        @endif
    </x-filament::modal>

</x-filament-panels::page>
