<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InventoryItemResource\Pages;
use App\Filament\Resources\InventoryItemResource\RelationManagers\TransactionsRelationManager;
use App\Models\InventoryItem;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;

class InventoryItemResource extends Resource
{
    protected static ?string $model = InventoryItem::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube';
    protected static ?string $navigationGroup = 'HR & Provoz';
    protected static ?string $navigationLabel = 'Sklad';
    protected static ?string $pluralModelLabel = 'Skladové položky';
    protected static ?string $modelLabel = 'Položka';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Základní informace')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Název')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('category')
                            ->label('Kategorie')
                            ->options([
                                'ingredient' => 'Ingredience (Kuchyně)',
                                'bar' => 'Bar',
                                'operational' => 'Provozní sklad (Spotřebák)',
                            ])
                            ->required(),
                        Forms\Components\TextInput::make('unit')
                            ->label('Jednotka')
                            ->placeholder('kg, l, ks')
                            ->required()
                            ->maxLength(10),
                    ])->columns(3),

                Forms\Components\Section::make('Nastavení skladu')
                    ->schema([
                        Forms\Components\TextInput::make('package_size')
                            ->label('Velikost balení (pro odpis)')
                            ->helperText('Kolik jednotek se odečte jedním kliknutím? (např. 1 pro 1l mléka, 25 pro 25kg pytel)')
                            ->numeric()
                            ->default(1)
                            ->required(),
                        Forms\Components\TextInput::make('min_stock_qty')
                            ->label('Minimální limit')
                            ->helperText('Hranice pro nákupní seznam')
                            ->numeric()
                            ->default(0)
                            ->required(),
                         Forms\Components\TextInput::make('price')
                            ->label('Nákupní cena (za jednotku)')
                            ->numeric()
                            ->prefix('CZK')
                            ->default(0),
                    ])->columns(3),

                Forms\Components\Section::make('Aktuální stav')
                    ->schema([
                        Forms\Components\TextInput::make('stock_qty')
                            ->label('Stav skladu')
                            ->numeric()
                            ->default(0)
                            ->disabled() // Prevent direct edit, encourage using actions? Or allow manual correction?
                            ->dehydrated() // Save even if disabled (if we enable it later or if creates need it)
                            ->helperText('Pro úpravu použijte funkci Nákup nebo Inventura.'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Název')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('category')
                    ->label('Kategorie')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'ingredient' => 'info',
                        'bar' => 'warning',
                        'operational' => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'ingredient' => 'Ingredience',
                        'bar' => 'Bar',
                        'operational' => 'Provozní',
                        default => $state,
                    })
                    ->sortable(),
                TextColumn::make('stock_qty')
                    ->label('Skladem')
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color(fn (InventoryItem $record): string => match ($record->stock_status) {
                        'critical' => 'danger',
                        'low' => 'warning',
                        'ok' => 'success',
                    }),
                TextColumn::make('unit')
                    ->label('Jedn.'),
                TextColumn::make('min_stock_qty')
                    ->label('Min. limit')
                    ->numeric()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('price')
                    ->label('Cena/j')
                    ->money('CZK')
                    ->sortable(),
                TextColumn::make('total_value')
                    ->label('Hodnota skladu')
                    ->state(fn (InventoryItem $record): float => $record->stock_qty * $record->price)
                    ->money('CZK')
                    ->sortable(),
            ])
            ->defaultSort('name')
            ->filters([
                Tables\Filters\Filter::make('shopping_list')
                    ->label('Nákupní seznam (Dochází)')
                    ->query(fn (Builder $query) => $query->whereColumn('stock_qty', '<=', 'min_stock_qty'))
                    ->default() // Optional: make it default if they want to see what to buy immediately? No, list all is better default.
                    ->toggle(),
                Tables\Filters\SelectFilter::make('category')
                    ->label('Kategorie')
                    ->options([
                        'ingredient' => 'Ingredience',
                        'bar' => 'Bar',
                        'operational' => 'Provozní',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('quick_add')
                    ->label('Příjem')
                    ->icon('heroicon-m-plus')
                    ->color('success')
                    ->button()
                    ->outlined()
                    ->size('xs')
                    ->form([
                        Forms\Components\TextInput::make('quantity')
                            ->label('Množství')
                            ->numeric()
                            ->default(fn (InventoryItem $record) => $record->package_size)
                            ->autofocus()
                            ->required(),
                    ])
                    ->action(function (InventoryItem $record, array $data) {
                        $qty = (float) $data['quantity'];

                        \Illuminate\Support\Facades\DB::transaction(function () use ($record, $qty) {
                            $record->stock_qty += $qty;
                            $record->save();

                            $record->transactions()->create([
                                'user_id' => auth()->id(),
                                'type' => 'purchase',
                                'quantity' => $qty,
                                'cost' => $record->price,
                                'note' => 'Rychlý příjem (Admin)',
                            ]);
                        });

                        \Filament\Notifications\Notification::make()->title('Přidáno')->success()->send();
                    }),

                Tables\Actions\Action::make('quick_subtract')
                    ->label('Výdej')
                    ->icon('heroicon-m-minus')
                    ->color('danger')
                    ->button()
                    ->outlined()
                    ->size('xs')
                    ->form([
                        Forms\Components\TextInput::make('quantity')
                            ->label('Množství')
                            ->numeric()
                            ->default(fn (InventoryItem $record) => $record->package_size)
                            ->autofocus()
                            ->required(),
                    ])
                    ->action(function (InventoryItem $record, array $data) {
                        $qty = (float) $data['quantity'];

                        \Illuminate\Support\Facades\DB::transaction(function () use ($record, $qty) {
                            $record->stock_qty -= $qty;
                            $record->save();

                            $record->transactions()->create([
                                'user_id' => auth()->id(),
                                'type' => 'write_off',
                                'quantity' => -$qty,
                                'cost' => $record->price,
                                'note' => 'Rychlý výdej (Admin)',
                            ]);
                        });

                        \Filament\Notifications\Notification::make()->title('Odebráno')->success()->send();
                    }),

                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            TransactionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInventoryItems::route('/'),
            'create' => Pages\CreateInventoryItem::route('/create'),
            'edit' => Pages\EditInventoryItem::route('/{record}/edit'),
        ];
    }
}
