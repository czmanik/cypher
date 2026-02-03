<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EventResource\Pages;
use App\Models\Event;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Get; // Důležité pro dynamické skrývání polí
use Filament\Forms\Components\Tabs;
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
                Tabs::make('Tabs')
                    ->tabs([
                        Tabs\Tab::make('Detail akce')
                            ->schema([
                                // 1. ZÁKLADNÍ INFORMACE
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

                                    Forms\Components\DateTimePicker::make('end_at')
                                        ->label('Konec (nepovinné)')
                                        ->afterOrEqual('start_at'),
                                ])->columns(2),

                                // 2. NOVÁ SEKCE PRO SLEVY A REGISTRACE
                                Forms\Components\Section::make('Nastavení kampaně / Slevy')
                                    ->description('Nastavte, pokud chcete sbírat kontakty nebo nabízet slevy (QR kód).')
                                    ->schema([
                                        Forms\Components\Toggle::make('is_commercial')
                                            ->label('Aktivovat sběr kontaktů / slevu')
                                            ->onColor('success')
                                            ->offColor('gray')
                                            ->live(), // Důležité: Reaguje okamžitě

                                        Forms\Components\Grid::make(2)
                                            ->schema([
                                                Forms\Components\TextInput::make('capacity_limit')
                                                    ->label('Kapacita (Počet voucherů)')
                                                    ->numeric()
                                                    ->suffix('ks')
                                                    ->placeholder('Neomezeně')
                                                    ->helperText('Nechte prázdné pro neomezený počet.'),

                                                Forms\Components\TextInput::make('offline_consumed_count')
                                                    ->label('Offline spotřeba')
                                                    ->numeric()
                                                    ->default(0)
                                                    ->helperText('Kolik kusů se rozdalo mimo web? (Odečte se z kapacity).'),

                                                Forms\Components\CheckboxList::make('required_fields')
                                                    ->label('Vyžadované údaje od zákazníka')
                                                    ->options([
                                                        'email' => 'Email',
                                                        'phone' => 'Telefonní číslo',
                                                        'instagram' => 'Instagram profil',
                                                    ])
                                                    ->columns(3)
                                                    ->helperText('Co musí uživatel vyplnit pro získání slevy?'),
                                            ])
                                            ->visible(fn (Get $get) => $get('is_commercial')), // Zobrazit jen když je zapnuto
                                    ])
                                    ->collapsible(),

                                // 3. OBSAH A MÉDIA
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
                            ]),

                        Tabs\Tab::make('SEO Optimalizace')
                            ->schema([
                                Forms\Components\Section::make('SEO Data')
                                    ->relationship('seo')
                                    ->schema([
                                        Forms\Components\TextInput::make('title')
                                            ->label('Meta Title')
                                            ->placeholder('Pokud prázdné, použije se název akce'),
                                        Forms\Components\Textarea::make('description')
                                            ->label('Meta Description')
                                            ->rows(2)
                                            ->placeholder('Pokud prázdné, použije se perex'),
                                        Forms\Components\FileUpload::make('image')
                                            ->label('OG Image (Sdílení)')
                                            ->image()
                                            ->directory('seo'),
                                        Forms\Components\Select::make('robots')
                                            ->label('Robots')
                                            ->options([
                                                'index, follow' => 'Index, Follow',
                                                'noindex, nofollow' => 'Noindex, Nofollow',
                                            ])
                                            ->default('index, follow'),
                                    ])
                            ]),
                    ])->columnSpanFull(),
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
                    ->searchable()
                    ->description(fn (Event $record) => Str::limit($record->perex, 30)),

                // Indikátor, že je akce slevová
                Tables\Columns\IconColumn::make('is_commercial')
                    ->label('Sleva')
                    ->boolean()
                    ->trueIcon('heroicon-o-tag')
                    ->falseIcon('heroicon-o-minus')
                    ->color(fn (string $state): string => $state ? 'warning' : 'gray'),

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
            'index' => Pages\ListEvents::route('/'),
            'create' => Pages\CreateEvent::route('/create'),
            'edit' => Pages\EditEvent::route('/{record}/edit'),
        ];
    }
}