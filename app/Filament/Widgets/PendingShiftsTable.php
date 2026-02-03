<?php

namespace App\Filament\Widgets;

use App\Models\WorkShift;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class PendingShiftsTable extends BaseWidget
{
    protected static ?int $sort = 2; 
    protected int | string | array $columnSpan = 'full'; 
    
    // Změníme nadpis, aby odpovídal realitě
    protected static ?string $heading = 'Směny vyžadující akci (Schválení & Platby)';

    public static function canView(): bool
    {
        return auth()->user()?->is_manager ?? false;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                WorkShift::query()
                    // Zobrazíme oboje: Co čeká na schválení I co čeká na peníze
                    ->whereIn('status', ['pending_approval', 'approved'])
                    ->orderBy('start_at', 'desc')
            )
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Zaměstnanec')
                    ->sortable()
                    ->weight('bold'),
                
                Tables\Columns\TextColumn::make('start_at')
                    ->label('Datum')
                    ->dateTime('d.m.Y'),

                Tables\Columns\TextColumn::make('total_hours')
                    ->label('Hodiny')
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('calculated_wage')
                    ->label('Mzda')
                    ->money('CZK')
                    ->weight('bold')
                    ->color(fn (WorkShift $record) => $record->status === 'approved' ? 'primary' : 'gray'),

                // Přidáme sloupec Status, abychom viděli rozdíl
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending_approval' => 'warning', // Oranžová
                        'approved' => 'primary',         // Modrá
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending_approval' => 'Ke kontrole',
                        'approved' => 'K úhradě',
                        default => $state,
                    }),
            ])
            ->actions([
                // AKCE 1: SCHVÁLIT (Jen pro Pending)
                Tables\Actions\Action::make('approve')
                    ->label('Schválit')
                    ->icon('heroicon-o-check')
                    ->color('warning') // Oranžová, aby ladila se statusem
                    ->button()
                    ->visible(fn (WorkShift $record) => $record->status === 'pending_approval')
                    ->action(fn (WorkShift $record) => $record->update(['status' => 'approved'])),

                // AKCE 2: PROPLATIT (Jen pro Approved)
                Tables\Actions\Action::make('mark_paid')
                    ->label('Proplatit')
                    ->icon('heroicon-o-currency-dollar')
                    ->color('success') // Zelená (finální krok)
                    ->button()
                    ->visible(fn (WorkShift $record) => $record->status === 'approved')
                    ->requiresConfirmation()
                    ->modalHeading('Potvrdit vyplacení')
                    ->modalDescription(fn (WorkShift $record) => 'Opravdu označit tuto směnu za proplacenou? Částka: ' . $record->calculated_wage . ' Kč')
                    ->action(fn (WorkShift $record) => $record->update(['status' => 'paid'])),

                // Editace (Vždy po ruce)
                Tables\Actions\EditAction::make()
                    ->icon('heroicon-o-pencil-square')
                    ->label('') // Bez textu, jen ikonka, ať to nezabírá místo
                    ->tooltip('Upravit směnu')
                    ->url(fn (WorkShift $record) => route('filament.admin.resources.work-shifts.edit', $record)),
            ]);
    }
}
