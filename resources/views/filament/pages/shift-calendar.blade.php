<x-filament-panels::page>
    {{-- Legenda barev (Vlastní sekce nad kalendářem) --}}
    <x-filament::section class="mb-4">
        <div class="flex flex-wrap gap-4 text-sm">
            <span class="font-bold text-gray-500">Legenda:</span>

            @if(auth()->user()->isManager())
                <div class="flex items-center gap-1">
                    <span class="w-3 h-3 rounded-full bg-gray-400 inline-block"></span>
                    <span>Draft (Vidíte jen vy)</span>
                </div>
            @endif

            <div class="flex items-center gap-1">
                <span class="w-3 h-3 rounded-full bg-blue-500 inline-block"></span>
                <span>Nařízeno (Platná směna)</span>
            </div>

            <div class="flex items-center gap-1">
                <span class="w-3 h-3 rounded-full bg-yellow-500 inline-block"></span>
                <span>Nabídka (K potvrzení)</span>
            </div>

            <div class="flex items-center gap-1">
                <span class="w-3 h-3 rounded-full bg-purple-500 inline-block"></span>
                <span>Žádost (Čeká na schválení)</span>
            </div>

            <div class="flex items-center gap-1">
                <span class="w-3 h-3 rounded-full bg-green-500 inline-block"></span>
                <span>Potvrzeno</span>
            </div>

            <div class="flex items-center gap-1">
                <span class="w-3 h-3 rounded-full bg-red-500 inline-block"></span>
                <span>Zamítnuto</span>
            </div>
        </div>
    </x-filament::section>

    {{-- Zde se vykreslí Widgety (tedy náš Kalendář) --}}
    @if ($headerWidgets = $this->getVisibleHeaderWidgets())
        <x-filament-widgets::widgets
            :widgets="$headerWidgets"
            :columns="$this->getHeaderWidgetsColumns()"
        />
    @endif

</x-filament-panels::page>
