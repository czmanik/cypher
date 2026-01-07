<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube'; // Ikona kostky pro produkty

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informace o produktu')->schema([
                    // Výběr kategorie z databáze
                    Forms\Components\Select::make('category_id')
                        ->relationship('category', 'name') // Vazba na Category, zobrazujeme 'name'
                        ->label('Kategorie')
                        ->searchable()
                        ->preload()
                        ->required(),

                    Forms\Components\TextInput::make('name')
                        ->label('Název produktu')
                        ->required()
                        ->maxLength(255),

                    Forms\Components\Textarea::make('description')
                        ->label('Popis / Složení')
                        ->rows(3)
                        ->columnSpanFull(),
                ])->columns(2),

                Forms\Components\Section::make('Cena a Sklad')->schema([
                    Forms\Components\TextInput::make('price')
                        ->label('Cena (CZK)')
                        ->numeric()
                        ->prefix('Kč')
                        ->required(),

                    Forms\Components\TextInput::make('stock_qty')
                        ->label('Skladem (ks)')
                        ->numeric()
                        ->helperText('Vyplň jen u zboží pro e-shop. U jídla nech prázdné.'),

                    Forms\Components\Toggle::make('is_shippable')
                        ->label('Je to zboží pro E-shop?')
                        ->default(false),
                    
                    Forms\Components\Toggle::make('is_available')
                        ->label('Dostupné (Zobrazit na webu)')
                        ->default(true),
                ])->columns(2),

                Forms\Components\Section::make('Média')->schema([
                    Forms\Components\FileUpload::make('image_path')
                        ->label('Fotka produktu')
                        ->image()
                        ->directory('products') // Uloží se do storage/app/public/products
                        ->imageEditor(), // Umožní ořez fotky přímo v adminu
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image_path')
                    ->label('Foto')
                    ->circular(), // Kulatý náhled

                Tables\Columns\TextColumn::make('name')
                    ->label('Název')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('category.name') // Tečková notace pro vazbu
                    ->label('Kategorie')
                    ->sortable()
                    ->badge(),

                Tables\Columns\TextColumn::make('price')
                    ->label('Cena')
                    ->money('CZK')
                    ->sortable(),

                Tables\Columns\ToggleColumn::make('is_available')
                    ->label('Dostupné'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->relationship('category', 'name')
                    ->label('Filtrovat dle kategorie'),
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}