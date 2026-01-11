<x-filament-widgets::widget>
    <x-filament::section>
        <h2 class="text-lg font-bold mb-4">Přehled směn</h2>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            {{-- PRÁVĚ TEĎ --}}
            <div class="space-y-2">
                <h3 class="font-medium text-gray-500 uppercase text-xs tracking-wider flex items-center gap-2">
                    <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                    Právě teď
                </h3>
                @forelse($activeShifts as $shift)
                    <div class="flex items-center justify-between p-2 bg-gray-50 dark:bg-gray-800 rounded border border-gray-100 dark:border-gray-700">
                        <div>
                            <div class="font-semibold text-sm">{{ $shift->user->name }}</div>
                            <div class="text-xs text-gray-500">{{ $shift->shift_role }}</div>
                        </div>
                        <div class="text-xs font-mono text-right">
                            {{ $shift->start_at->format('H:i') }} - {{ $shift->end_at->format('H:i') }}
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-400 italic">Nikdo není na směně.</p>
                @endforelse
            </div>

            {{-- DNES POZDĚJI --}}
            <div class="space-y-2">
                <h3 class="font-medium text-gray-500 uppercase text-xs tracking-wider">
                    Dnes později
                </h3>
                @forelse($upcomingShifts as $shift)
                    <div class="flex items-center justify-between p-2 bg-white dark:bg-gray-900 rounded border border-gray-100 dark:border-gray-700">
                        <div>
                            <div class="font-semibold text-sm">{{ $shift->user->name }}</div>
                            <div class="text-xs text-gray-500">{{ $shift->shift_role }}</div>
                        </div>
                        <div class="text-xs font-mono text-right">
                            {{ $shift->start_at->format('H:i') }}
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-400 italic">Žádné další směny dnes.</p>
                @endforelse
            </div>

            {{-- ZÍTRA --}}
            <div class="space-y-2">
                <h3 class="font-medium text-gray-500 uppercase text-xs tracking-wider">
                    Zítra
                </h3>
                @forelse($tomorrowShifts as $shift)
                    <div class="flex items-center justify-between p-2 bg-white dark:bg-gray-900 rounded border border-gray-100 dark:border-gray-700">
                        <div>
                            <div class="font-semibold text-sm">{{ $shift->user->name }}</div>
                            <div class="text-xs text-gray-500">{{ $shift->shift_role }}</div>
                        </div>
                        <div class="text-xs font-mono text-right">
                            {{ $shift->start_at->format('H:i') }}
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-400 italic">Na zítra není nic naplánováno.</p>
                @endforelse
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
