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
                Tržby za vybraný den
            </div>
        </x-filament::section>

        <!-- Labor Cost -->
        <x-filament::section>
            <x-slot name="heading">
                Náklady na personál
            </x-slot>
            <div class="text-3xl font-bold text-danger-600 dark:text-danger-400">
                {{ number_format($laborCost, 0, ',', ' ') }} Kč
            </div>
            <div class="text-sm text-gray-500 mt-2">
                Mzdy za směny (včetně probíhajících)
            </div>
        </x-filament::section>

        <!-- Profit -->
        <x-filament::section>
            <x-slot name="heading">
                Hrubý zisk (Tržba - Mzdy)
            </x-slot>
            <div class="text-3xl font-bold {{ $profit >= 0 ? 'text-primary-600' : 'text-danger-600' }}">
                {{ number_format($profit, 0, ',', ' ') }} Kč
            </div>
            <div class="text-sm text-gray-500 mt-2">
                Rozdíl mezi tržbou a náklady na směny
            </div>
        </x-filament::section>
    </div>

    <!-- Shifts Breakdown -->
    <x-filament::section>
        <x-slot name="heading">
            Detail směn a nákladů
        </x-slot>

        @if(count($shifts) > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-3">Zaměstnanec</th>
                            <th scope="col" class="px-6 py-3">Stav</th>
                            <th scope="col" class="px-6 py-3">Typ mzdy</th>
                            <th scope="col" class="px-6 py-3">Začátek</th>
                            <th scope="col" class="px-6 py-3">Konec</th>
                            <th scope="col" class="px-6 py-3">Hodiny (cca)</th>
                            <th scope="col" class="px-6 py-3 text-right">Náklad (Kč)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($shifts as $shift)
                            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                    {{ $shift['user_name'] }}
                                </td>
                                <td class="px-6 py-4">
                                    @if($shift['status'] === 'Probíhá')
                                        <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-green-900 dark:text-green-300">
                                            Probíhá
                                        </span>
                                    @else
                                        <span class="bg-gray-100 text-gray-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-gray-700 dark:text-gray-300">
                                            Uzavřeno
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    {{ $shift['salary_type'] }}
                                </td>
                                <td class="px-6 py-4">
                                    {{ $shift['start'] }}
                                </td>
                                <td class="px-6 py-4">
                                    {{ $shift['end'] }}
                                </td>
                                <td class="px-6 py-4">
                                    {{ number_format($shift['hours'], 2) }} h
                                </td>
                                <td class="px-6 py-4 text-right font-bold text-danger-600">
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
