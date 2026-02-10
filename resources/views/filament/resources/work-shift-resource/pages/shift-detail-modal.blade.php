<div class="space-y-6">
    {{-- Header Stats --}}
    <div class="grid grid-cols-2 gap-4">
        <div class="p-4 bg-gray-50 rounded-lg dark:bg-gray-800">
            <div class="text-sm text-gray-500 dark:text-gray-400">Zaměstnanec</div>
            <div class="text-lg font-bold text-gray-900 dark:text-white">{{ $record->user->name }}</div>
        </div>
        <div class="p-4 bg-gray-50 rounded-lg dark:bg-gray-800">
            <div class="text-sm text-gray-500 dark:text-gray-400">Datum a Čas</div>
            <div class="text-lg font-bold text-gray-900 dark:text-white">
                {{ $record->start_at->format('d.m.Y') }} ({{ $record->total_hours }}h)
            </div>
            <div class="text-sm text-gray-600 dark:text-gray-300">
                {{ $record->start_at->format('H:i') }} - {{ $record->end_at ? $record->end_at->format('H:i') : '...' }}
            </div>
        </div>
    </div>

    {{-- Financial Breakdown --}}
    <div class="border rounded-lg dark:border-gray-700">
        <div class="px-4 py-2 bg-gray-100 dark:bg-gray-700 font-semibold text-gray-900 dark:text-white rounded-t-lg">
            Finanční přehled
        </div>
        <div class="p-4 space-y-2">
            <div class="flex justify-between">
                <span>Základní mzda:</span>
                <span class="font-medium">{{ number_format($record->calculated_wage, 2, ',', ' ') }} Kč</span>
            </div>

            @if($record->bonus > 0)
                <div class="flex justify-between text-green-600">
                    <span>Bonus:</span>
                    <span class="font-medium">+{{ number_format($record->bonus, 2, ',', ' ') }} Kč</span>
                </div>
                @if($record->bonus_note)
                    <div class="text-xs text-green-700 italic ml-4">
                        "{{ $record->bonus_note }}"
                    </div>
                @endif
            @endif

            @if($record->penalty > 0)
                <div class="flex justify-between text-red-600">
                    <span>Pokuta (Malus):</span>
                    <span class="font-medium">-{{ number_format($record->penalty, 2, ',', ' ') }} Kč</span>
                </div>
                @if($record->penalty_note)
                    <div class="text-xs text-red-700 italic ml-4">
                        "{{ $record->penalty_note }}"
                    </div>
                @endif
            @endif

            @if($record->advance_amount > 0)
                <div class="flex justify-between text-orange-600">
                    <span>Záloha:</span>
                    <span class="font-medium">-{{ number_format($record->advance_amount, 2, ',', ' ') }} Kč</span>
                </div>
            @endif

            <hr class="border-gray-200 dark:border-gray-600 my-2">

            <div class="flex justify-between text-lg font-bold">
                <span>K VÝPLATĚ:</span>
                <span class="text-primary-600">{{ number_format($record->final_payout, 2, ',', ' ') }} Kč</span>
            </div>
             @if($record->status === 'paid')
                <div class="text-right text-xs text-green-600 font-medium mt-1">
                    PROPLACENO ({{ $record->payment_method === 'bank_transfer' ? 'Na účet' : 'Hotově' }})
                </div>
            @endif
        </div>
    </div>

    {{-- Notes --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="p-4 border rounded-lg dark:border-gray-700">
            <div class="text-xs uppercase font-bold text-gray-500 mb-2">Poznámka zaměstnance</div>
            <p class="text-sm text-gray-700 dark:text-gray-300">{{ $record->general_note ?? '---' }}</p>
        </div>
        <div class="p-4 border rounded-lg dark:border-gray-700 bg-yellow-50 dark:bg-yellow-900/10">
            <div class="text-xs uppercase font-bold text-yellow-600 mb-2">Poznámka manažera</div>
            <p class="text-sm text-gray-700 dark:text-gray-300">{{ $record->manager_note ?? '---' }}</p>
        </div>
    </div>

    {{-- Checklist --}}
    <div class="border rounded-lg dark:border-gray-700">
        <div class="px-4 py-2 bg-gray-100 dark:bg-gray-700 font-semibold text-gray-900 dark:text-white rounded-t-lg">
            Checklist
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-4 py-2">Úkol</th>
                        <th scope="col" class="px-4 py-2">Stav</th>
                        <th scope="col" class="px-4 py-2">Poznámka</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($record->checklistResults as $result)
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 last:border-b-0">
                            <td class="px-4 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                {{ $result->task_name }}
                            </td>
                            <td class="px-4 py-2">
                                @if($result->is_completed)
                                    <span class="text-green-600 font-bold">✓ Splněno</span>
                                @else
                                    <span class="text-red-600 font-bold">✗ Nesplněno</span>
                                @endif
                            </td>
                            <td class="px-4 py-2">
                                {{ $result->note }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-4 py-2 text-center">Žádné záznamy v checklistu.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Activity Log --}}
    <div class="border rounded-lg dark:border-gray-700 mt-6">
        <div class="px-4 py-2 bg-gray-100 dark:bg-gray-700 font-semibold text-gray-900 dark:text-white rounded-t-lg">
            Historie změn
        </div>
        <div class="p-4 space-y-4 max-h-60 overflow-y-auto">
            @forelse($record->activities()->latest()->get() as $activity)
                <div class="border-l-4 border-gray-300 pl-4 py-2">
                    <div class="flex justify-between items-center mb-1">
                        <span class="font-bold text-sm text-gray-800 dark:text-gray-200">
                            {{ $activity->causer?->name ?? 'Systém' }}
                        </span>
                        <span class="text-xs text-gray-500">
                            {{ $activity->created_at->format('d.m.Y H:i') }}
                        </span>
                    </div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        {{ ucfirst($activity->description) }}
                    </p>
                    @if($activity->properties->has('attributes'))
                        <div class="mt-1 text-xs text-gray-500">
                            @foreach($activity->properties['attributes'] as $key => $newVal)
                                @php
                                    $oldVal = $activity->properties['old'][$key] ?? null;
                                    if($newVal == $oldVal) continue;
                                @endphp
                                <div>
                                    <strong>{{ $key }}:</strong>
                                    <span class="line-through text-red-400">{{ $oldVal ?? '---' }}</span>
                                    ➝
                                    <span class="text-green-600 font-medium">{{ $newVal }}</span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            @empty
                <div class="text-sm text-gray-500 text-center">Žádné změny.</div>
            @endforelse
        </div>
    </div>
</div>
