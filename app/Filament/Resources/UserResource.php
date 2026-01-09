<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'HR & Provoz';
    protected static ?string $navigationLabel = 'Zaměstnanci';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // 1. SEKCE: OSOBNÍ ÚDAJE
                Section::make('Osobní údaje')
                    ->description('Základní informace o uživateli')
                    ->schema([
                        TextInput::make('name')
                            ->label('Jméno a Příjmení')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('email')
                            ->label('E-mail')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),

                        TextInput::make('phone')
                            ->label('Telefon')
                            ->tel()
                            ->maxLength(20),

                        TextInput::make('password')
                            ->label('Heslo')
                            ->password()
                            ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $operation): bool => $operation === 'create'),
                    ])->columns(2),

                // 2. SEKCE: HR & DOCHÁZKA
                Section::make('HR & Nastavení Zaměstnance')
                    ->description('Nastavení pozice, mzdy a přístupů')
                    ->schema([
                        TextInput::make('pin_code')
                            ->label('PIN Kód (4 číslice)')
                            ->numeric()
                            ->length(4)
                            ->password()     // Způsobí, že jsou vidět tečky ••••
                            ->revealable()   // Přidá ikonku "oka" pro zobrazení
                            ->required()
                            ->helperText('Slouží pro rychlé přihlášení na tabletu.'),

                        Select::make('employee_type')
                            ->label('Oddělení / Pozice')
                            ->options([
                                'manager' => 'Management / Majitel',
                                'kitchen' => 'Kuchyň',
                                'floor' => 'Plac / Bar',
                                'support' => 'Pomocný personál',
                            ])
                            ->required(),

                        Select::make('salary_type')
                            ->label('Typ Mzdy')
                            ->options([
                                'hourly' => 'Hodinová sazba',
                                'fixed' => 'Fixní za směnu',
                            ])
                            ->default('hourly')
                            ->required(),

                        TextInput::make('hourly_rate')
                            ->label('Sazba (Kč)')
                            ->numeric()
                            ->prefix('Kč')
                            ->helperText('Cena za hodinu nebo za celou směnu.'),

                        Toggle::make('is_manager')
                            ->label('Je Manažer?')
                            ->helperText('Manažer může schvalovat směny a vidí přehledy.')
                            ->onColor('success')
                            ->offColor('danger'),

                        Toggle::make('is_active')
                            ->label('Aktivní zaměstnanec')
                            ->default(true)
                            ->helperText('Vypnutím zamezíte přihlášení do systému.'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Jméno')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('phone')
                    ->label('Telefon')
                    ->searchable(),

                // Sloupec pro Pozici (Barevný štítek)
                Tables\Columns\TextColumn::make('employee_type')
                    ->label('Pozice')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'manager' => 'primary',
                        'kitchen' => 'warning', // Oranžová
                        'floor' => 'success',   // Zelená
                        'support' => 'gray',    // Šedá
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'manager' => 'Management',
                        'kitchen' => 'Kuchyň',
                        'floor' => 'Plac',
                        'support' => 'Pomocná',
                        default => $state,
                    }),

                // Sloupec pro Manažera (Ikona)
                Tables\Columns\IconColumn::make('is_manager')
                    ->label('Manažer')
                    ->boolean()
                    ->trueIcon('heroicon-o-shield-check')
                    ->falseIcon('heroicon-o-user'),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktivní')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Jen aktivní'),
                Tables\Filters\SelectFilter::make('employee_type')
                    ->label('Oddělení')
                    ->options([
                        'manager' => 'Management / Majitel',
                        'kitchen' => 'Kuchyň',
                        'floor' => 'Plac / Bar',
                        'support' => 'Pomocný',
                    ]),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}