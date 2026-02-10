<?php

namespace App\Filament\Resources\InventoryItemResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class TransactionsRelationManager extends RelationManager
{
    protected static string $relationship = 'transactions';

    protected static ?string $title = 'Historie pohybů';
    protected static ?string $modelLabel = 'Pohyb';
    protected static ?string $pluralModelLabel = 'Pohyby';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('type')
                    ->disabled(),
                Forms\Components\TextInput::make('quantity')
                    ->disabled(),
                Forms\Components\TextInput::make('note')
                    ->disabled(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Datum a čas')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('Typ pohybu')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'purchase' => 'Nákup (Příjem)',
                        'write_off' => 'Odpis (Výdej)',
                        'adjustment' => 'Korekce',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'purchase' => 'success',
                        'write_off' => 'danger',
                        'adjustment' => 'warning',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('quantity')
                    ->label('Množství')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('cost')
                    ->label('Cena/j')
                    ->money('CZK')
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Uživatel')
                    ->sortable(),
                Tables\Columns\TextColumn::make('note')
                    ->label('Poznámka')
                    ->wrap(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->headerActions([
                // Tables\Actions\CreateAction::make(), // Disable manual creation for now
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
                // Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
            ]);
    }
}
