<?php

namespace App\Filament\Pages;

use App\Models\InventoryItem;
use App\Models\InventoryTransaction;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;

class FastWriteOff extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-bolt';
    protected static ?string $navigationGroup = 'Moje Práce';
    protected static ?string $navigationLabel = 'Rychlý výdej';
    protected static ?string $title = 'Rychlý výdej ze skladu';
    protected static string $view = 'filament.pages.fast-write-off';

    public static function canAccess(): bool
    {
        return (bool) auth()->user()?->is_active;
    }

    public array $selectedItems = []; // [itemId => count]

    #[Computed]
    public function items(): Collection
    {
        return InventoryItem::query()
            ->orderBy('category')
            ->orderBy('name')
            ->get();
    }

    #[Computed]
    public function selectedItemsData(): Collection
    {
        if (empty($this->selectedItems)) {
            return collect();
        }

        return InventoryItem::whereIn('id', array_keys($this->selectedItems))
            ->get()
            ->keyBy('id');
    }

    public function toggleItem(int $itemId): void
    {
        if (isset($this->selectedItems[$itemId])) {
            $this->selectedItems[$itemId]++;
        } else {
            $this->selectedItems[$itemId] = 1;
        }
    }

    public function removeItem(int $itemId): void
    {
        if (isset($this->selectedItems[$itemId])) {
            $this->selectedItems[$itemId]--;
            if ($this->selectedItems[$itemId] <= 0) {
                unset($this->selectedItems[$itemId]);
            }
        }
    }

    public function clearSelection(): void
    {
        $this->selectedItems = [];
    }

    public function submit(): void
    {
        if (empty($this->selectedItems)) {
            Notification::make()
                ->title('Žádné položky k odpisu')
                ->warning()
                ->send();
            return;
        }

        DB::transaction(function () {
            foreach ($this->selectedItems as $itemId => $count) {
                // Lock the row to prevent race conditions on stock update
                $item = InventoryItem::lockForUpdate()->find($itemId);
                if (!$item) continue;

                $totalDeduction = $count * $item->package_size;

                // Decrement stock (atomic update within transaction)
                $item->decrement('stock_qty', $totalDeduction);

                InventoryTransaction::create([
                    'inventory_item_id' => $item->id,
                    'user_id' => auth()->id(),
                    'type' => 'write_off',
                    'quantity' => -$totalDeduction,
                    'cost' => $item->price, // Cost at time of write-off
                    'note' => "Rychlý výdej: {$count}x balení ({$item->package_size} {$item->unit})",
                ]);
            }
        });

        Notification::make()
            ->title('Odpis byl úspěšně proveden')
            ->success()
            ->send();

        $this->selectedItems = [];
    }
}
