<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EventClaimResource\Pages;
use App\Models\EventClaim;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

class EventClaimResource extends Resource
{
    protected static ?string $model = EventClaim::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'Marketing & Data'; // Nová skupina v menu
    protected static ?string $navigationLabel = 'Získané kontakty';

    // Formulář necháme jen pro čtení (readonly), data měnit nechceme
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('email')->readOnly(),
                Forms\Components\TextInput::make('phone')->readOnly(),
                Forms\Components\TextInput::make('instagram')->readOnly(),
                Forms\Components\DateTimePicker::make('created_at')->label('Vytvořeno')->readOnly(),
                Forms\Components\TextInput::make('claim_token')->label('Kód')->readOnly(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                // Sloupec Akce
                Tables\Columns\TextColumn::make('event.title')
                    ->label('Kampaň')
                    ->sortable()
                    ->searchable(),

                // Kontaktní údaje (umožníme hledat podle všech)
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->icon('heroicon-m-envelope'),
                    
                Tables\Columns\TextColumn::make('phone')
                    ->searchable()
                    ->toggleable(), // Lze skrýt
                    
                Tables\Columns\TextColumn::make('instagram')
                    ->searchable()
                    ->toggleable(),

                // Kdy to vzniklo
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Datum registrace')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),

                // Stav (Uplatněno / Neuplatněno)
                Tables\Columns\IconColumn::make('redeemed_at')
                    ->label('Uplatněno?')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-clock')
                    ->color(fn ($state) => $state ? 'success' : 'warning'),
            ])
            ->filters([
                // 1. Filtr podle Kampaně
                SelectFilter::make('event')
                    ->label('Kampaň')
                    ->relationship('event', 'title'),

                // 2. Filtr "Jen uplatněné"
                Filter::make('redeemed')
                    ->label('Jen uplatněné vouchery')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('redeemed_at')),
                
                // 3. Filtr podle data (Dnes, Tento týden...)
                 Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')->label('Od data'),
                        Forms\Components\DatePicker::make('created_until')->label('Do data'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })
            ]);
    }
    
    // ... getPages ...
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEventClaims::route('/'),
            // Create a Edit nepotřebujeme, lidi se registrují sami
        ];
    }
}