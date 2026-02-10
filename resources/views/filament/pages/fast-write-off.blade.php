<x-filament-panels::page>
    <div class="flex flex-col md:flex-row gap-6">
        <!-- Main Grid Area -->
        <div class="flex-1">
            @php
                // Group items by category for display
                $groupedItems = $this->items->groupBy('category');
            @endphp

            @foreach($groupedItems as $category => $categoryItems)
                <div class="mb-6">
                    <h2 class="text-xl font-bold mb-4 capitalize">
                        {{ $category === 'ingredient' ? 'Ingredience' : 'Spotřebák' }}
                    </h2>
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
                        @foreach($categoryItems as $item)
                            <div
                                wire:click="toggleItem({{ $item->id }})"
                                class="cursor-pointer bg-white dark:bg-gray-800 rounded-xl shadow-sm hover:shadow-md transition border-2 p-4 relative group select-none
                                {{ isset($this->selectedItems[$item->id]) ? 'border-primary-500 bg-primary-50 dark:bg-primary-900/10' : 'border-transparent' }}
                                "
                            >
                                <div class="flex justify-between items-start mb-2">
                                    <span class="font-semibold text-lg line-clamp-2 leading-tight">{{ $item->name }}</span>
                                    @if($item->stock_status === 'critical')
                                        <div class="w-3 h-3 rounded-full bg-red-500" title="Kritický stav"></div>
                                    @elseif($item->stock_status === 'low')
                                        <div class="w-3 h-3 rounded-full bg-orange-500" title="Nízký stav"></div>
                                    @endif
                                </div>

                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ $item->package_size }} {{ $item->unit }} / klik
                                </div>

                                @if(isset($this->selectedItems[$item->id]))
                                    <div class="absolute top-2 right-2 bg-primary-600 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs font-bold">
                                        {{ $this->selectedItems[$item->id] }}
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Sidebar / Summary -->
        <div class="w-full md:w-80 flex-shrink-0">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 sticky top-6">
                <h3 class="text-lg font-bold mb-4">K výdeji</h3>

                @if(empty($this->selectedItems))
                    <div class="text-gray-500 text-center py-8">
                        Vyberte položky kliknutím
                    </div>
                @else
                    <div class="space-y-3 mb-6">
                        @foreach($this->selectedItems as $itemId => $count)
                            @php
                                // Efficient lookup from pre-loaded collection
                                $item = $this->selectedItemsData->get($itemId);
                            @endphp
                            @if($item)
                                <div class="flex justify-between items-center group">
                                    <div class="flex-1">
                                        <div class="font-medium">{{ $item->name }}</div>
                                        <div class="text-xs text-gray-500">{{ $count * $item->package_size }} {{ $item->unit }} celkem</div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <button wire:click="removeItem({{ $itemId }})" class="text-gray-400 hover:text-red-500 p-1">
                                            <x-heroicon-m-minus-circle class="w-5 h-5"/>
                                        </button>
                                        <span class="font-bold w-4 text-center">{{ $count }}</span>
                                        <button wire:click="toggleItem({{ $itemId }})" class="text-gray-400 hover:text-primary-500 p-1">
                                            <x-heroicon-m-plus-circle class="w-5 h-5"/>
                                        </button>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>

                    <div class="flex flex-col gap-2">
                        <x-filament::button wire:click="submit" size="lg" class="w-full">
                            Potvrdit výdej
                        </x-filament::button>

                        <x-filament::button wire:click="clearSelection" color="gray" size="sm" class="w-full">
                            Zrušit vše
                        </x-filament::button>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-filament-panels::page>
