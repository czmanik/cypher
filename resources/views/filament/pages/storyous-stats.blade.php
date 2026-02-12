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
                    <!-- Assuming 'items' key exists if Storyous provides detail, but bills list usually doesn't have items. -->
                    <!-- Usually detailed bill requires another API call /bills/{billId}, but for now we show what we have in the list object. -->
                    <!-- The list object has 'payments', 'taxes', etc. -->

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
