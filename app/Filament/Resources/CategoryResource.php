<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str; 

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag'; // Ikona v menu

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Sekce pro základní info
                Forms\Components\Section::make('Základní informace')->schema([
                    
                    Forms\Components\TextInput::make('name')
                        ->label('Název kategorie')
                        ->required()
                        ->maxLength(255)
                        // Tohle zajistí, že když píšeš název, automaticky se vyplňuje slug
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn (string $operation, $state, Forms\Set $set) => 
                            $operation === 'create' ? $set('slug', Str::slug($state)) : null
                        ),

                    Forms\Components\TextInput::make('slug')
                        ->required()
                        ->maxLength(255)
                        ->unique(ignoreRecord: true), // Musí být unikátní
                ]),

                // Sekce pro nastavení
                Forms\Components\Section::make('Nastavení')->schema([
                    
                    Forms\Components\Select::make('type')
                        ->label('Typ zobrazení')
                        ->options([
                            'menu' => 'Jídelní lístek',
                            'eshop' => 'E-shop',
                            'both' => 'Obojí',
                        ])
                        ->required()
                        ->default('menu'),

                    Forms\Components\TextInput::make('sort_order')
                        ->label('Pořadí')
                        ->numeric()
                        ->default(0),

                    Forms\Components\Toggle::make('is_visible')
                        ->label('Zobrazit na webu')
                        ->default(true),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Název')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('type')
                    ->label('Typ')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'menu' => 'info',
                        'eshop' => 'success',
                        'both' => 'warning',
                    }),

                Tables\Columns\ToggleColumn::make('is_visible')
                    ->label('Viditelnost'), // Umožní přepínat přímo v tabulce

                Tables\Columns\TextColumn::make('products_count')
                    ->counts('products') // Zobrazí počet produktů v kategorii
                    ->label('Počet produktů'),
            ])
            ->filters([
                // Tady můžeme časem přidat filtry
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
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }
}