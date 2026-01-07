<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReservationResource\Pages;
use App\Models\Reservation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table as FilamentTable;

class ReservationResource extends Resource
{
    protected static ?string $model = Reservation::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days'; // Ikona kalendáře
    protected static ?string $navigationLabel = 'Rezervace';
    protected static ?int $navigationSort = 1; // Aby to bylo nahoře v menu

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Detail rezervace')->schema([
                    // Výběr stolu - zobrazujeme název a kapacitu
                    Forms\Components\Select::make('table_id')
                        ->relationship('table', 'name')
                        ->label('Stůl')
                        ->required()
                        ->searchable()
                        ->preload(),

                    Forms\Components\DateTimePicker::make('reservation_time')
                        ->label('Datum a čas')
                        ->seconds(false)
                        ->required(),

                    Forms\Components\TextInput::make('guests_count')
                        ->label('Počet hostů')
                        ->numeric()
                        ->required(),
                        
                    Forms\Components\TextInput::make('duration_minutes')
                        ->label('Délka (min)')
                        ->numeric()
                        ->default(90),
                ])->columns(2),

                Forms\Components\Section::make('Zákazník')->schema([
                    Forms\Components\TextInput::make('customer_name')
                        ->label('Jméno')
                        ->required(),
                    
                    Forms\Components\TextInput::make('customer_phone')
                        ->label('Telefon')
                        ->tel()
                        ->required(),

                    Forms\Components\TextInput::make('customer_email')
                        ->label('E-mail')
                        ->email(),
                        
                    Forms\Components\Textarea::make('note')
                        ->label('Poznámka')
                        ->columnSpanFull(),
                ])->columns(2),

                Forms\Components\Section::make('Stav')->schema([
                    Forms\Components\Select::make('status')
                        ->options([
                            'pending' => 'Čeká na potvrzení',
                            'confirmed' => 'Potvrzeno',
                            'completed' => 'Usazeno / Hotovo',
                            'cancelled' => 'Zrušeno',
                        ])
                        ->default('confirmed')
                        ->required(),
                ]),
            ]);
    }

    public static function table(FilamentTable $table): FilamentTable
    {
        return $table
            ->defaultSort('reservation_time', 'desc') // Nejnovější nahoře
            ->columns([
                Tables\Columns\TextColumn::make('reservation_time')
                    ->label('Datum a Čas')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('customer_name')
                    ->label('Zákazník')
                    ->searchable()
                    ->description(fn (Reservation $record): string => $record->customer_phone), // Telefon pod jménem

                Tables\Columns\TextColumn::make('table.name')
                    ->label('Stůl')
                    ->sortable(),

                Tables\Columns\TextColumn::make('guests_count')
                    ->label('Osob')
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'confirmed' => 'success',
                        'completed' => 'info',
                        'cancelled' => 'danger',
                    })
                    ->label('Stav'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Čekající',
                        'confirmed' => 'Potvrzené',
                        'cancelled' => 'Zrušené',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListReservations::route('/'),
            'create' => Pages\CreateReservation::route('/create'),
            'edit' => Pages\EditReservation::route('/{record}/edit'),
        ];
    }
}