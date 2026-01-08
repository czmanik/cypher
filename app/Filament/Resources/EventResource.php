<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EventResource\Pages;
use App\Models\Event;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class EventResource extends Resource
{
    protected static ?string $model = Event::class;

    protected static ?string $navigationIcon = 'heroicon-o-ticket';
    protected static ?string $navigationGroup = 'Obsah webu';
    protected static ?string $navigationLabel = 'Akce a Události';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Hlavní info')->schema([
                    Forms\Components\TextInput::make('title')
                        ->label('Název akce')
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn (string $operation, $state, Forms\Set $set) => 
                            $operation === 'create' ? $set('slug', Str::slug($state)) : null
                        )
                        ->required(),

                    Forms\Components\Select::make('category')
                        ->label('Kategorie')
                        ->options([
                            'kultura' => 'Kultura (Výstava, Hudba)',
                            'gastro' => 'Gastro (Jídlo, Brunch)',
                            'piti' => 'Pití (Bar, Degustace)',
                        ])
                        ->required()
                        ->default('kultura'),

                    Forms\Components\TextInput::make('slug')
                        ->required()
                        ->unique(ignoreRecord: true),
                        
                    Forms\Components\DateTimePicker::make('start_at')
                        ->label('Začátek')
                        ->required(),

                    Forms\Components\DateTimePicker::make('end_at') //
                        ->label('Konec (nepovinné)')
                        ->afterOrEqual('start_at'), 
                ])->columns(2),

                Forms\Components\Section::make('Obsah')->schema([
                    Forms\Components\Textarea::make('perex')
                        ->label('Krátký úvod (na kartičku)')
                        ->rows(3)
                        ->required(),

                    Forms\Components\RichEditor::make('description')
                        ->label('Plný popis akce')
                        ->fileAttachmentsDirectory('events')
                        ->columnSpanFull(),

                    Forms\Components\FileUpload::make('image_path')
                        ->label('Plakát / Fotka')
                        ->image()
                        ->directory('events'),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('start_at', 'desc')
            ->columns([
                Tables\Columns\ImageColumn::make('image_path')
                    ->circular()
                    ->label('Img'),
                    
                Tables\Columns\TextColumn::make('title')
                    ->label('Název')
                    ->searchable(),

                Tables\Columns\TextColumn::make('start_at')
                    ->label('Kdy')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),

                Tables\Columns\ToggleColumn::make('is_published')
                    ->label('Publikováno'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEvents::route('/'),
            'create' => Pages\CreateEvent::route('/create'),
            'edit' => Pages\EditEvent::route('/{record}/edit'),
        ];
    }
}