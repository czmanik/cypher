<x-filament-widgets::widget>
    <x-filament::section>
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-bold">Můj Checklist (Aktivní směna)</h2>
            <span class="text-sm text-gray-500">{{ $activeShift->start_at->format('H:i') }} - ...</span>
        </div>

        @if($activeShift)
            <div class="space-y-2">
                @foreach($checklistItems as $item)
                    <div class="flex items-center space-x-3 p-2 rounded hover:bg-gray-50 dark:hover:bg-gray-800">
                        <input
                            type="checkbox"
                            wire:click="toggleItem({{ $item->id }})"
                            @checked($item->is_completed)
                            class="w-5 h-5 text-primary-600 rounded border-gray-300 focus:ring-primary-500"
                        />
                        <span class="@if($item->is_completed) line-through text-gray-400 @endif">
                            {{ $item->task_name }}
                        </span>
                    </div>
                @endforeach
            </div>

            @if(count($checklistItems) === 0)
                <p class="text-gray-500 italic">Žádné úkoly pro tuto směnu.</p>
            @endif
        @else
            <p>Žádná aktivní směna.</p>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
