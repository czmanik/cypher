<?php

namespace App\Filament\Widgets;

use App\Models\PlannedShift;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Notifications\Notification;

class ShiftChangeRequestsWidget extends BaseWidget
{
    protected static ?int $sort = 1;
    protected int | string | array $columnSpan = 'full';
    protected static ?string $heading = 'Žádosti o změnu směny';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                PlannedShift::query()
                    ->where('status', 'request_change')
                    ->with('user')
            )
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Zaměstnanec')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('start_at')
                    ->label('Datum a Čas')
                    ->formatStateUsing(fn (PlannedShift $record) => $record->start_at->format('d.m.Y H:i') . ' - ' . $record->end_at->format('H:i')),

                Tables\Columns\TextColumn::make('employee_comment')
                    ->label('Důvod')
                    ->wrap(),
            ])
            ->actions([
                // Action to acknowledge (e.g., Delete shift because they can't make it)
                Tables\Actions\DeleteAction::make()
                    ->label('Zrušit směnu'),

                // Action to re-confirm (e.g. after talking to them)
                Tables\Actions\Action::make('reconfirm')
                    ->label('Vnutit (Potvrdit)')
                    ->color('warning')
                    ->action(function (PlannedShift $record) {
                        $record->update(['status' => 'confirmed']);
                        Notification::make()->title('Směna potvrzena manažerem')->success()->send();
                    }),
            ]);
    }
}
