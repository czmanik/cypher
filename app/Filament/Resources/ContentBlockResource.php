<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContentBlockResource\Pages;
use App\Models\ContentBlock;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ContentBlockResource extends Resource
{
    protected static ?string $model = ContentBlock::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Obsah webu';
    protected static ?string $navigationLabel = 'Texty a Bloky';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('key')
                    ->label('Systémový klíč')
                    ->helperText('Např. homepage_hero. Neměnit, pokud nevíš co děláš.')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('title')
                    ->label('Nadpis sekce')
                    ->required()
                    ->maxLength(255),

                Forms\Components\FileUpload::make('image_path')
                    ->label('Obrázek na pozadí / Hlavní foto')
                    ->image()
                    ->directory('content'),

                Forms\Components\RichEditor::make('content')
                    ->label('Textový obsah')
                    ->fileAttachmentsDirectory('content_images')
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

                Tables\Columns\TextColumn::make('key')
                    ->label('Klíč (ID)')
                    ->badge()
                    ->color('gray'),
                
                Tables\Columns\ImageColumn::make('image_path')
                    ->label('Obrázek'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListContentBlocks::route('/'),
            'create' => Pages\CreateContentBlock::route('/create'),
            'edit' => Pages\EditContentBlock::route('/{record}/edit'),
        ];
    }
}