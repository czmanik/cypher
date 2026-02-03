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
                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                    <td class="px-4 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                        {{ $result->task_name }}
                    </td>
                    <td class="px-4 py-2">
                        @if($result->is_completed)
                            <span class="bg-green-100 text-green-800 text-xs font-medium mr-2 px-2.5 py-0.5 rounded dark:bg-green-900 dark:text-green-300">Splněno</span>
                        @else
                            <span class="bg-red-100 text-red-800 text-xs font-medium mr-2 px-2.5 py-0.5 rounded dark:bg-red-900 dark:text-red-300">Nesplněno</span>
                        @endif
                    </td>
                    <td class="px-4 py-2">
                        {{ $result->note ?? '-' }}
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
