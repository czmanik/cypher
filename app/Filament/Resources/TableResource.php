<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TableResource\Pages;
use App\Models\Table;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table as FilamentTable;

class TableResource extends Resource
{
    protected static ?string $model = Table::class;

    protected static ?string $navigationIcon = 'heroicon-o-map'; // Ikona mapy/rozložení
    protected static ?string $navigationGroup = 'Nastavení'; // Zatřídíme to bokem
    protected static ?string $modelLabel = 'Stůl';
    protected static ?string $pluralModelLabel = 'Stoly';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Název stolu')
                    ->required()
                    ->placeholder('Např. Stůl u okna'),
                
                Forms\Components\TextInput::make('capacity')
                    ->label('Počet míst')
                    ->numeric()
                    ->required(),

                Forms\Components\Select::make('location')
                    ->label('Umístění')
                    ->options([
                        'indoor' => 'Uvnitř',
                        'garden' => 'Zahrádka',
                    ])
                    ->required()
                    ->default('indoor'),

                Forms\Components\Toggle::make('is_active')
                    ->label('Aktivní (lze rezervovat)')
                    ->default(true),
            ]);
    }

    public static function table(FilamentTable $table): FilamentTable
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Stůl')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('capacity')
                    ->label('Kapacita')
                    ->sortable(),

                Tables\Columns\TextColumn::make('location')
                    ->label('Lokace')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'indoor' => 'info',
                        'garden' => 'success',
                    }),

                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Aktivní'),
            ])
            ->filters([
                //
            ])
            ->actions([
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTables::route('/'),
            'create' => Pages\CreateTable::route('/create'),
            'edit' => Pages\EditTable::route('/{record}/edit'),
        ];
    }
}