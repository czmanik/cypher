<x-filament-panels::page>
    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
        <!-- Revenue -->
        <x-filament::section>
            <x-slot name="heading">
                Celková tržba (Storyous)
            </x-slot>
            <div class="text-3xl font-bold text-success-600 dark:text-success-400">
                {{ number_format($revenue, 0, ',', ' ') }} Kč
            </div>
            <div class="text-sm text-gray-500 mt-2">
                Zahrnuje všechny zaplacené účtenky.
            </div>
        </x-filament::section>

        <!-- Labor Costs -->
        <x-filament::section>
            <x-slot name="heading">
                Náklady na mzdy
            </x-slot>
            <div class="text-3xl font-bold text-danger-600 dark:text-danger-400">
                {{ number_format($laborCosts, 0, ',', ' ') }} Kč
            </div>
            <div class="text-sm text-gray-500 mt-2">
                Mzdy zaměstnanců na směně (včetně aktivních).
            </div>
        </x-filament::section>

        <!-- Net Result -->
        <x-filament::section>
            <x-slot name="heading">
                Hrubý zisk (Tržba - Mzdy)
            </x-slot>
            <div class="text-3xl font-bold {{ $profit >= 0 ? 'text-primary-600 dark:text-primary-400' : 'text-danger-600 dark:text-danger-400' }}">
                {{ number_format($profit, 0, ',', ' ') }} Kč
            </div>
            <div class="text-sm text-gray-500 mt-2">
                Výsledek hospodaření pro tento den (před zdaněním a surovinami).
            </div>
        </x-filament::section>
    </div>

    <!-- Shifts Detail Table -->
    <x-filament::section>
        <x-slot name="heading">
            Detail směn a mzdových nákladů
        </x-slot>

        @if(count($shifts) > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-3">Zaměstnanec</th>
                            <th scope="col" class="px-6 py-3">Start</th>
                            <th scope="col" class="px-6 py-3">Konec</th>
                            <th scope="col" class="px-6 py-3">Stav</th>
                            <th scope="col" class="px-6 py-3 text-right">Náklad (Kč)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($shifts as $shift)
                            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                    {{ $shift['user_name'] }}
                                </td>
                                <td class="px-6 py-4">
                                    {{ $shift['start_at'] }}
                                </td>
                                <td class="px-6 py-4">
                                    {{ $shift['end_at'] }}
                                </td>
                                <td class="px-6 py-4">
                                    <x-filament::badge :color="match($shift['status']) {
                                        'active' => 'success',
                                        'pending_approval' => 'warning',
                                        'approved' => 'info',
                                        'paid' => 'success',
                                        'rejected' => 'danger',
                                        default => 'gray',
                                    }">
                                        {{ match($shift['status']) {
                                            'active' => 'Aktivní',
                                            'pending_approval' => 'Čeká na schválení',
                                            'approved' => 'Schváleno',
                                            'paid' => 'Proplaceno',
                                            'rejected' => 'Zamítnuto',
                                            default => $shift['status'],
                                        } }}
                                    </x-filament::badge>
                                </td>
                                <td class="px-6 py-4 text-right font-bold">
                                    {{ number_format($shift['cost'], 0, ',', ' ') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="p-4 text-center text-gray-500">
                Žádné směny pro tento den.
            </div>
        @endif
    </x-filament::section>
</x-filament-panels::page>
