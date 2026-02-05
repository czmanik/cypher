<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RecipeResource\Pages;
use App\Models\Recipe;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class RecipeResource extends Resource
{
    protected static ?string $model = Recipe::class;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';
    protected static ?string $navigationGroup = 'HR & Provoz';
    protected static ?string $navigationLabel = 'Recepty';
    protected static ?string $modelLabel = 'Recept';
    protected static ?string $pluralModelLabel = 'Recepty';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Základní informace')
                    ->schema([
                        Forms\Components\Select::make('product_id')
                            ->relationship('product', 'name')
                            ->label('Produkt')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('yield')
                            ->label('Výtěžnost')
                            ->placeholder('např. 4 porce nebo 2 l')
                            ->maxLength(255),

                        Forms\Components\TextInput::make('prep_time')
                            ->label('Čas přípravy')
                            ->placeholder('např. 45 min')
                            ->maxLength(255),

                        Forms\Components\TextInput::make('temperature')
                            ->label('Teplota')
                            ->placeholder('např. 180 °C')
                            ->maxLength(255),

                        Forms\Components\TextInput::make('video_url')
                            ->label('Odkaz na video')
                            ->url()
                            ->suffixIcon('heroicon-m-video-camera')
                            ->placeholder('https://youtube.com/...')
                            ->maxLength(255),
                    ])->columns(2),

                Forms\Components\Section::make('Oprávnění')
                    ->description('Kdo může tento recept vidět?')
                    ->schema([
                        Forms\Components\CheckboxList::make('allowed_roles')
                            ->label('Povolené role')
                            ->options([
                                User::TYPE_KITCHEN => 'Kuchyň',
                                User::TYPE_FLOOR => 'Plac / Bar',
                                User::TYPE_SUPPORT => 'Pomocný personál',
                                User::TYPE_MANAGER => 'Management',
                            ])
                            ->columns(2)
                            ->gridDirection('row'),
                    ]),

                Forms\Components\Section::make('Ingredience')
                    ->schema([
                        Forms\Components\Repeater::make('ingredients')
                            ->label('Seznam ingrediencí')
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('Ingredience')
                                    ->required(),
                                Forms\Components\TextInput::make('amount')
                                    ->label('Množství')
                                    ->required(),
                            ])
                            ->columns(2)
                            ->addActionLabel('Přidat ingredienci')
                            ->defaultItems(1),
                    ]),

                Forms\Components\Section::make('Postup přípravy')
                    ->schema([
                        Forms\Components\RichEditor::make('description')
                            ->label('Popis postupu')
                            ->required()
                            ->fileAttachmentsDirectory('recipes/content'),
                    ]),

                Forms\Components\Section::make('Fotogalerie')
                    ->schema([
                        Forms\Components\FileUpload::make('images')
                            ->label('Fotografie')
                            ->image()
                            ->multiple()
                            ->reorderable()
                            ->directory('recipes')
                            ->imageEditor()
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('images')
                    ->label('Foto')
                    ->circular()
                    ->stacked()
                    ->limit(3),

                Tables\Columns\TextColumn::make('product.name')
                    ->label('Produkt')
                    ->searchable()
                    ->sortable()
                    ->description(fn (Recipe $record) => $record->product?->category?->name ?? ''),

                Tables\Columns\TextColumn::make('yield')
                    ->label('Výtěžnost')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('prep_time')
                    ->label('Čas')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('allowed_roles')
                    ->label('Role')
                    ->badge()
                    ->separator(',')
                    ->formatStateUsing(fn ($state): string => match ((string) $state) {
                        User::TYPE_KITCHEN => 'Kuchyň',
                        User::TYPE_FLOOR => 'Plac',
                        User::TYPE_SUPPORT => 'Pomoc',
                        User::TYPE_MANAGER => 'Manager',
                        default => (string) $state,
                    })
                    ->color(fn ($state): string => match ((string) $state) {
                        User::TYPE_KITCHEN => 'danger',
                        User::TYPE_FLOOR => 'warning',
                        User::TYPE_SUPPORT => 'gray',
                        User::TYPE_MANAGER => 'success',
                        default => 'gray',
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('role')
                    ->label('Dle role')
                    ->options([
                        User::TYPE_KITCHEN => 'Kuchyň',
                        User::TYPE_FLOOR => 'Plac / Bar',
                        User::TYPE_SUPPORT => 'Pomocný personál',
                        User::TYPE_MANAGER => 'Management',
                    ])
                    ->query(fn ($query, $state) => $query->whereJsonContains('allowed_roles', $state['value'])),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Detaily receptu')
                    ->schema([
                        Infolists\Components\ImageEntry::make('images')
                            ->hiddenLabel()
                            ->columnSpanFull()
                            ->height(300),

                        Infolists\Components\Grid::make(3)->schema([
                            Infolists\Components\TextEntry::make('yield')
                                ->label('Výtěžnost')
                                ->icon('heroicon-m-scale'),

                            Infolists\Components\TextEntry::make('prep_time')
                                ->label('Čas')
                                ->icon('heroicon-m-clock'),

                            Infolists\Components\TextEntry::make('temperature')
                                ->label('Teplota')
                                ->icon('heroicon-m-fire'),
                        ]),
                    ]),

                Infolists\Components\Section::make('Ingredience')
                    ->schema([
                        Infolists\Components\RepeatableEntry::make('ingredients')
                            ->hiddenLabel()
                            ->schema([
                                Infolists\Components\Grid::make(2)->schema([
                                    Infolists\Components\TextEntry::make('name')
                                        ->hiddenLabel()
                                        ->weight('bold'),
                                    Infolists\Components\TextEntry::make('amount')
                                        ->hiddenLabel()
                                        ->alignRight(),
                                ]),
                            ])
                            ->grid(1)
                            ->contained(false),
                    ]),

                Infolists\Components\Section::make('Postup')
                    ->schema([
                        Infolists\Components\TextEntry::make('description')
                            ->hiddenLabel()
                            ->html()
                            ->columnSpanFull(),
                    ]),

                Infolists\Components\Section::make('Video')
                    ->visible(fn (Recipe $record) => !empty($record->video_url))
                    ->schema([
                        Infolists\Components\TextEntry::make('video_url')
                            ->hiddenLabel()
                            ->formatStateUsing(fn ($state) => self::getEmbedHtml($state))
                            ->html()
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    // --- ADDED EXPLICIT QUERY METHOD ---
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes();
    }
    // -----------------------------------

    protected static function getEmbedHtml($url) {
        if (str_contains($url, 'youtube.com') || str_contains($url, 'youtu.be')) {
            preg_match('/(youtu\.be\/|youtube\.com\/(watch\?(.*&)?v=|(embed|v)\/))([^\?&"\'>]+)/', $url, $matches);
            if(isset($matches[5])){
                $id = $matches[5];
                return '<iframe width="100%" height="315" src="https://www.youtube.com/embed/'.$id.'" frameborder="0" allowfullscreen></iframe>';
            }
        }
        return '<a href="'.htmlspecialchars($url).'" target="_blank" class="text-primary-600 hover:underline">Otevřít video</a>';
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
            'index' => Pages\ListRecipes::route('/'),
            'create' => Pages\CreateRecipe::route('/create'),
            'view' => Pages\ViewRecipe::route('/{record}'),
            'edit' => Pages\EditRecipe::route('/{record}/edit'),
        ];
    }

}
