<?php

namespace App\Filament\Resources\InventoryItemResource\Pages;

use App\Filament\Resources\InventoryItemResource;
use App\Models\InventoryItem;
use App\Models\InventoryTransaction;
use Filament\Actions;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class ListInventoryItems extends ListRecords
{
    protected static string $resource = InventoryItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Nová položka'),

            Actions\Action::make('purchase')
                ->label('Příjem zboží (Nákup)')
                ->icon('heroicon-o-shopping-bag')
                ->color('success')
                ->form([
                    Repeater::make('items')
                        ->schema([
                            Select::make('inventory_item_id')
                                ->label('Položka')
                                ->options(InventoryItem::pluck('name', 'id'))
                                ->searchable()
                                ->required()
                                ->reactive()
                                ->afterStateUpdated(function ($state, callable $set) {
                                    if ($state) {
                                        $item = InventoryItem::find($state);
                                        if ($item) {
                                            $set('price', $item->price);
                                            $set('package_info', "Balení: {$item->package_size} {$item->unit}");
                                        }
                                    }
                                }),
                            TextInput::make('package_info')
                                ->label('Info o balení')
                                ->disabled()
                                ->dehydrated(false), // Don't submit this
                            TextInput::make('quantity')
                                ->label('Počet balení')
                                ->numeric()
                                ->default(1)
                                ->required(),
                            TextInput::make('price')
                                ->label('Cena za jednotku (CZK)')
                                ->numeric()
                                ->prefix('CZK')
                                ->required(),
                        ])
                        ->columns(4)
                        ->defaultItems(0)
                        ->addActionLabel('Přidat další položku'),
                ])
                ->action(function (array $data) {
                    DB::transaction(function () use ($data) {
                        foreach ($data['items'] as $itemData) {
                            $item = InventoryItem::find($itemData['inventory_item_id']);
                            if (!$item) continue;

                            $qtyPackages = (float) $itemData['quantity'];
                            $unitPrice = (float) $itemData['price'];
                            $addedStock = $qtyPackages * $item->package_size;

                            // Update Item
                            $item->stock_qty += $addedStock;
                            $item->price = $unitPrice; // Update current price
                            $item->save();

                            // Create Transaction
                            InventoryTransaction::create([
                                'inventory_item_id' => $item->id,
                                'user_id' => auth()->id(),
                                'type' => 'purchase',
                                'quantity' => $addedStock,
                                'cost' => $unitPrice,
                                'note' => "Nákup: {$qtyPackages} balení (á {$item->package_size} {$item->unit})",
                            ]);
                        }
                    });

                    Notification::make()
                        ->title('Nákup byl úspěšně uložen')
                        ->success()
                        ->send();
                }),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Všechny položky'),
            'shopping_list' => Tab::make('Nákupní seznam')
                ->icon('heroicon-m-shopping-cart')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereColumn('stock_qty', '<=', 'min_stock_qty'))
                ->badge(InventoryItem::whereColumn('stock_qty', '<=', 'min_stock_qty')->count())
                ->badgeColor('danger'),
            'ingredients' => Tab::make('Ingredience')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('category', 'ingredient')),
            'operational' => Tab::make('Spotřebák')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('category', 'operational')),
        ];
    }
}
