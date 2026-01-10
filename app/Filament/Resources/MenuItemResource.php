<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MenuItemResource\Pages;
use App\Models\MenuItem;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Get; // Důležité pro reaktivitu

class MenuItemResource extends Resource
{
    protected static ?string $model = MenuItem::class;

    protected static ?string $navigationIcon = 'heroicon-o-bars-3'; // Ikonka menu
    protected static ?string $navigationLabel = 'Položky Menu';
    protected static ?string $navigationGroup = 'Nastavení webu'; // Aby to bylo hezky uklizené

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('label')
                    ->label('Text tlačítka')
                    ->required(),

                Select::make('type')
                    ->label('Typ odkazu')
                    ->options([
                        'page' => 'Stránka z administrace',
                        'route' => 'Systémová sekce (Menu, Rezervace...)',
                        'url' => 'Externí odkaz (Instagram...)',
                    ])
                    ->default('page')
                    ->live(), // <--- Reaguje na změnu hned

                // 1. Zobrazit jen pokud je typ = PAGE
                Select::make('page_id')
                    ->label('Vyber stránku')
                    ->relationship('page', 'title') 
                    ->required(fn (Get $get) => $get('type') === 'page')
                    ->visible(fn (Get $get) => $get('type') === 'page'),

                // 2. Zobrazit jen pokud je typ = ROUTE
                Select::make('route_name')
                    ->label('Systémová cesta')
                    ->options([
                        'menu' => 'Denní Menu',
                        'events.index' => 'Kalendář Akcí',
                        'reservations.create' => 'Rezervace',
                        'home' => 'Domů',
                    ])
                    ->required(fn (Get $get) => $get('type') === 'route')
                    ->visible(fn (Get $get) => $get('type') === 'route'),

                // 3. Zobrazit jen pokud je typ = URL
                TextInput::make('url')
                    ->label('Webová adresa (https://...)')
                    ->url()
                    ->required(fn (Get $get) => $get('type') === 'url')
                    ->visible(fn (Get $get) => $get('type') === 'url'),

                TextInput::make('sort_order')
                    ->label('Pořadí')
                    ->numeric()
                    ->default(0),

                Toggle::make('new_tab')
                    ->label('Otevřít v novém okně'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->reorderable('sort_order') // Umožní přetahování myší
            ->defaultSort('sort_order')
            ->columns([
                Tables\Columns\TextColumn::make('label')
                    ->label('Název')
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('type')
                    ->label('Typ')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'page' => 'success',
                        'route' => 'info',
                        'url' => 'warning',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'page' => 'Stránka',
                        'route' => 'Systém',
                        'url' => 'Odkaz',
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMenuItems::route('/'),
            'create' => Pages\CreateMenuItem::route('/create'),
            'edit' => Pages\EditMenuItem::route('/{record}/edit'),
        ];
    }
}