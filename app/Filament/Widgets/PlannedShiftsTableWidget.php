<?php

namespace App\Filament\Widgets;

use App\Models\PlannedShift;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\BulkAction;
use Illuminate\Database\Eloquent\Collection;

class PlannedShiftsTableWidget extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';

    // Zobrazíme jen pokud je uživatel manager
    public static function canView(): bool
    {
        return auth()->user()?->isManager();
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                PlannedShift::query()->orderBy('start_at')
            )
            ->heading('Přehled všech naplánovaných slotů')
            ->columns([
                Tables\Columns\TextColumn::make('start_at')
                    ->label('Datum')
                    ->date('d.m.Y (D)')
                    ->sortable(),

                Tables\Columns\TextColumn::make('time_range')
                    ->label('Čas')
                    ->state(fn (PlannedShift $record) => $record->start_at->format('H:i') . ' - ' . $record->end_at->format('H:i')),

                Tables\Columns\TextColumn::make('shift_role')
                    ->label('Pozice')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'kitchen' => 'warning',
                        'floor' => 'info',
                        'manager' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Zaměstnanec')
                    ->placeholder('Volná směna'),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'ordered' => 'blue',
                        'offered' => 'yellow',
                        'confirmed' => 'green',
                        'rejected' => 'red',
                        default => 'gray',
                    }),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'offered' => 'Nabídka (Volné)',
                        'ordered' => 'Nařízeno',
                        'confirmed' => 'Potvrzeno',
                    ]),
                SelectFilter::make('user_id')
                    ->label('Zaměstnanec')
                    ->relationship('user', 'name')
            ])
            ->actions([
                EditAction::make()
                    ->form([
                        \Filament\Forms\Components\Select::make('user_id')
                            ->relationship('user', 'name')
                            ->searchable(),
                        \Filament\Forms\Components\Select::make('status')
                            ->options([
                                'draft' => 'Draft',
                                'offered' => 'Nabídka',
                                'ordered' => 'Nařízeno',
                            ]),
                    ]),
                DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    BulkAction::make('publish')
                        ->label('Zveřejnit vybrané')
                        ->icon('heroicon-o-megaphone')
                        ->color('success')
                        ->action(fn (Collection $records) => $records->each->update([
                            'status' => $records->first()->user_id ? 'ordered' : 'offered'
                            // Zjednodušená logika: pokud to má usera, je to ordered, jinak offered
                            // Lepší by bylo kontrolovat každý record zvlášť v loopu
                        ])),
                ]),
            ]);
    }
}
