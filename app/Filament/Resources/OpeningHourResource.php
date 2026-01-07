<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OpeningHourResource\Pages;
use App\Models\OpeningHour;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class OpeningHourResource extends Resource
{
    protected static ?string $model = OpeningHour::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';
    protected static ?string $navigationGroup = 'Nastavení';
    protected static ?string $navigationLabel = 'Otevírací doba';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Nastavení dne')->schema([
                    Forms\Components\Select::make('day_of_week')
                        ->label('Den v týdnu')
                        ->options([
                            1 => 'Pondělí',
                            2 => 'Úterý',
                            3 => 'Středa',
                            4 => 'Čtvrtek',
                            5 => 'Pátek',
                            6 => 'Sobota',
                            7 => 'Neděle',
                        ])
                        ->disabled() // Den by se neměl měnit, jen časy
                        ->required(),
                    
                    Forms\Components\Toggle::make('is_closed')
                        ->label('Zavřeno celý den')
                        ->columnSpanFull(),
                ])->columns(2),

                Forms\Components\Section::make('Časy')->schema([
                    Forms\Components\TimePicker::make('bar_open')->label('Bar Otevírá')->seconds(false),
                    Forms\Components\TimePicker::make('bar_close')->label('Bar Zavírá')->seconds(false),
                    
                    Forms\Components\TimePicker::make('kitchen_open')->label('Kuchyně Otevírá')->seconds(false),
                    Forms\Components\TimePicker::make('kitchen_close')->label('Kuchyně Zavírá')->seconds(false),
                ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('day_of_week')
                    ->label('Den')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        '1' => 'Pondělí',
                        '2' => 'Úterý',
                        '3' => 'Středa',
                        '4' => 'Čtvrtek',
                        '5' => 'Pátek',
                        '6' => 'Sobota',
                        '7' => 'Neděle',
                        default => $state,
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('bar_open')
                    ->label('Bar')
                    ->formatStateUsing(fn (OpeningHour $record) => $record->is_closed ? 'ZAVŘENO' : "$record->bar_open - $record->bar_close"),

                Tables\Columns\TextColumn::make('kitchen_open')
                    ->label('Kuchyně')
                    ->formatStateUsing(fn (OpeningHour $record) => $record->is_closed ? '-' : "$record->kitchen_open - $record->kitchen_close"),
            ])
            ->paginated(false) // Máme jen 7 dní, stránkování netřeba
            ->actions([
                Tables\Actions\EditAction::make(),
            ]);
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOpeningHours::route('/'),
            // 'create' vypneme, protože dny už máme vytvořené seederem a nechceme přidávat 8. den
            // 'create' => Pages\CreateOpeningHour::route('/create'), 
            'edit' => Pages\EditOpeningHour::route('/{record}/edit'),
        ];
    }
}