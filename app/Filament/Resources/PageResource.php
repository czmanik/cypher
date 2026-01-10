<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PageResource\Pages;
use App\Models\Page;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;

class PageResource extends Resource
{
    protected static ?string $model = Page::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'Stránky';
    protected static ?string $navigationGroup = 'Obsah webu'; // Tady to zařadíme do menu

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Nastavení stránky')
                    ->schema([
                        TextInput::make('title')
                            ->label('Název stránky')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn ($set, $state) => $set('slug', str()->slug($state))),
                        
                        TextInput::make('slug')
                            ->label('URL adresa (např. o-nas)')
                            ->required()
                            ->unique(ignoreRecord: true),
                            
                        Toggle::make('is_active')
                            ->label('Zveřejněno')
                            ->default(true),
                    ])->columns(3),

                // STAVEBNICE OBSAHU
                Builder::make('content')
                    ->label('Obsah stránky (Bloky)')
                    ->blocks([
                        // 1. BLOK: Hero (Velká fotka)
                        Builder\Block::make('hero')
                            ->label('Hero Sekce (Velká fotka)')
                            ->schema([
                                TextInput::make('headline')->label('Hlavní nadpis')->required(),
                                Textarea::make('subheadline')->label('Podnadpis'),
                                FileUpload::make('image')
                                    ->label('Fotka na pozadí')
                                    ->image()
                                    ->directory('pages-hero'),
                            ]),

                        // 2. BLOK: Text + Fotka
                        Builder\Block::make('text_image')
                            ->label('Text a Fotka')
                            ->schema([
                                TextInput::make('title')->label('Nadpis sekce'),
                                RichEditor::make('text')->label('Text'),
                                FileUpload::make('image')->image()->directory('pages-content'),
                                Select::make('layout')
                                    ->options([
                                        'left' => 'Fotka vlevo',
                                        'right' => 'Fotka vpravo',
                                    ])->default('left'),
                            ]),

                        // 3. BLOK: Citát
                        Builder\Block::make('quote')
                            ->label('Citát')
                            ->schema([
                                Textarea::make('text')->required(),
                                TextInput::make('author')->label('Autor'),
                            ]),
                    ])
                    ->collapsible()
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Název')
                    ->searchable(),
                Tables\Columns\TextColumn::make('slug')
                    ->label('URL')
                    ->prefix('/'),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktivní')
                    ->boolean(),
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
            'index' => Pages\ListPages::route('/'),
            'create' => Pages\CreatePage::route('/create'),
            'edit' => Pages\EditPage::route('/{record}/edit'),
        ];
    }
}