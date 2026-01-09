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

    protected static ?string $navigationIcon = 'heroicon-o-users'; // Ikona uživatelů
    protected static ?string $navigationGroup = 'HR & Provoz';     // <-- PŘESUNUTO
    protected static ?string $navigationLabel = 'Zaměstnanci';     // <-- PŘEJMENOVÁNO
    protected static ?int $navigationSort = 1;                     // Bude první v sekci

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
                            ->dehydrated(fn ($state) => filled($state)) // Uloží se jen když je vyplněno
                            ->required(fn (string $operation): bool => $operation === 'create'), // Povinné jen při zakládání
                    ])->columns(2),

                // 2. SEKCE: HR & DOCHÁZKA
                Section::make('HR & Nastavení Zaměstnance')
                    ->description('Nastavení pro docházkový systém')
                    ->schema([
                        TextInput::make('pin_code')
                            ->label('PIN Kód (4 číslice)')
                            ->numeric()
                            ->length(4)
                            ->unique(ignoreRecord: true)
                            ->password()
                            ->revealable()
                            ->required()
                            ->helperText('Slouží pro rychlé přihlášení na tabletu.'),

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

                        Toggle::make('is_active')
                            ->label('Aktivní zaměstnanec')
                            ->default(true)
                            ->helperText('Vypnutím zamezíte přihlášení do systému.')
                            ->columnSpanFull(),
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

                Tables\Columns\TextColumn::make('email')
                    ->label('E-mail')
                    ->searchable(),

                Tables\Columns\TextColumn::make('phone')
                    ->label('Telefon')
                    ->searchable(),

                Tables\Columns\TextColumn::make('salary_type')
                    ->label('Typ mzdy')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'hourly' => 'info',
                        'fixed' => 'warning',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'hourly' => 'Hodinová',
                        'fixed' => 'Fixní',
                    }),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktivní')
                    ->boolean(),
            ])
            ->filters([
                // Můžeme přidat filtr pro aktivní/neaktivní
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Jen aktivní'),
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