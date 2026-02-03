<x-filament-widgets::widget>
    <x-filament::section>
        <div class="flex items-center justify-between">
            <div class="flex flex-col">
                <h2 class="text-xl font-bold">Moje Směna</h2>
                @if($activeShift)
                    <div class="text-sm text-gray-500">
                        Začátek: <span class="font-medium">{{ $activeShift->start_at->format('H:i') }}</span>
                        <span class="mx-1">&bull;</span>
                        Délka: <span class="font-medium">{{ $activeShift->start_at->diffForHumans(['parts' => 2, 'join' => true, 'syntax' => \Carbon\CarbonInterface::DIFF_ABSOLUTE]) }}</span>
                    </div>
                @else
                    <div class="text-sm text-gray-500">
                        Aktuálně nemáte žádnou aktivní směnu.
                    </div>
                @endif
            </div>

            <div class="flex items-center gap-3">
                @if($activeShift)
                    {{ $this->endShiftAction }}
                @else
                    {{ $this->startShiftAction }}
                @endif
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
