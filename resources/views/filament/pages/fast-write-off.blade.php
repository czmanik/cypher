<x-filament-panels::page>
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 items-start">
        <!-- Main Grid Area -->
        <div class="lg:col-span-3 min-w-0 order-2 lg:order-1">
            @php
                // Group items by category for display
                $groupedItems = $this->items->groupBy('category');
            @endphp

            @foreach($groupedItems as $category => $categoryItems)
                <div class="mb-8">
                    <h2 class="text-xl font-bold mb-4 flex items-center gap-2 border-b border-gray-200 dark:border-gray-700 pb-2">
                        @if($category === 'ingredient')
                            <x-heroicon-o-beaker class="w-6 h-6 text-primary-500"/>
                            <span>Ingredience (Kuchyně)</span>
                        @else
                            <x-heroicon-o-cube class="w-6 h-6 text-gray-500"/>
                            <span>Provozní sklad (Spotřebák)</span>
                        @endif
                    </h2>
                    <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-4">
                        @foreach($categoryItems as $item)
                            <div
                                wire:click="toggleItem({{ $item->id }})"
                                class="flex flex-col h-full justify-between cursor-pointer bg-white dark:bg-gray-800 rounded-xl shadow-sm hover:shadow-md transition border-2 p-4 relative group select-none
                                {{ isset($this->selectedItems[$item->id]) ? 'border-primary-500 bg-primary-50 dark:bg-primary-900/10' : 'border-transparent' }}
                                "
                            >
                                <div>
                                    <div class="flex justify-between items-start mb-2 gap-2">
                                        <span class="font-semibold text-lg line-clamp-2 leading-tight">{{ $item->name }}</span>
                                        @if($item->stock_status === 'critical')
                                            <div class="flex-shrink-0 w-3 h-3 rounded-full bg-red-500 animate-pulse" title="Kritický stav! (Došlo)"></div>
                                        @elseif($item->stock_status === 'low')
                                            <div class="flex-shrink-0 w-3 h-3 rounded-full bg-orange-500" title="Nízký stav (Objednat)"></div>
                                        @else
                                            <div class="flex-shrink-0 w-3 h-3 rounded-full bg-green-500" title="Skladem"></div>
                                        @endif
                                    </div>

                                    <div class="text-sm text-gray-500 dark:text-gray-400 mb-3">
                                        Odpis: <span class="font-medium text-gray-900 dark:text-gray-200">{{ (float) $item->package_size }} {{ $item->unit }}</span>
                                    </div>
                                </div>

                                <div class="mt-auto pt-3 border-t border-gray-100 dark:border-gray-700 flex justify-between items-center text-sm">
                                    <span class="text-gray-500">Skladem:</span>
                                    <span class="font-bold {{ $item->stock_qty <= $item->min_stock_qty ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-white' }}">
                                        {{ (float) $item->stock_qty }} {{ $item->unit }}
                                    </span>
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
        <div class="lg:col-span-1 w-full order-1 lg:order-2">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 lg:sticky lg:top-24 z-10">
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
                                        <div class="text-xs text-gray-500">{{ (float) ($count * $item->package_size) }} {{ $item->unit }} celkem</div>
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
