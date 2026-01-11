<?php

namespace App\Filament\Widgets;

use App\Models\PlannedShift;
use App\Models\ShiftAuditLog;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ShiftChangeRequestsWidget extends BaseWidget
{
    protected static ?int $sort = 1;
    protected int | string | array $columnSpan = 'full';
    protected static ?string $heading = 'Schvalování a Žádosti';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                PlannedShift::query()
                    // Show:
                    // 1. request_change (existing logic: employee can't make it)
                    // 2. pending AND user_id IS NOT NULL (new logic: someone claimed an open shift)
                    ->where(function (Builder $query) {
                        $query->where('status', 'request_change')
                              ->orWhere(function (Builder $q) {
                                  $q->where('status', 'pending')
                                    ->whereNotNull('user_id');
                              });
                    })
                    ->with('user')
                    ->orderBy('start_at')
            )
            ->columns([
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Typ')
                    ->colors([
                        'warning' => 'pending', // Claim Request
                        'danger' => 'request_change', // Change Request
                    ])
                    ->formatStateUsing(fn (string $state, PlannedShift $record): string => match ($state) {
                        'pending' => 'Nová přihláška',
                        'request_change' => 'Žádost o změnu',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Zaměstnanec')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('start_at')
                    ->label('Datum a Čas')
                    ->formatStateUsing(fn (PlannedShift $record) => $record->start_at->format('d.m.Y H:i') . ' - ' . $record->end_at->format('H:i')),

                Tables\Columns\TextColumn::make('employee_comment')
                    ->label('Komentář / Důvod')
                    ->wrap()
                    ->placeholder('-'),
            ])
            ->actions([
                // --- CLAIM REQUEST ACTIONS (For Pending) ---

                Tables\Actions\Action::make('approve_claim')
                    ->label('Schválit')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->button()
                    ->visible(fn (PlannedShift $record) => $record->status === 'pending')
                    ->action(function (PlannedShift $record) {
                        $record->update(['status' => 'confirmed']);

                        ShiftAuditLog::create([
                            'planned_shift_id' => $record->id,
                            'user_id' => Auth::id(),
                            'action' => 'approved',
                        ]);

                        Notification::make()->title('Zaměstnanec přiřazen a směna potvrzena.')->success()->send();
                    }),

                Tables\Actions\Action::make('reject_claim')
                    ->label('Zamítnout')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->button()
                    ->visible(fn (PlannedShift $record) => $record->status === 'pending')
                    ->action(function (PlannedShift $record) {
                        $oldUser = $record->user_id;

                        // Reset to open shift
                        $record->update([
                            'user_id' => null,
                            'status' => 'pending', // Pending but no user = Open Shift in calendar
                        ]);

                        ShiftAuditLog::create([
                            'planned_shift_id' => $record->id,
                            'user_id' => Auth::id(),
                            'action' => 'rejected',
                            'payload' => ['rejected_user_id' => $oldUser],
                        ]);

                        Notification::make()->title('Žádost zamítnuta, směna je opět volná.')->success()->send();
                    }),


                // --- CHANGE REQUEST ACTIONS (Existing) ---

                Tables\Actions\DeleteAction::make()
                    ->label('Zrušit směnu')
                    ->visible(fn (PlannedShift $record) => $record->status === 'request_change'),

                Tables\Actions\Action::make('reconfirm')
                    ->label('Vnutit (Potvrdit)')
                    ->color('warning')
                    ->visible(fn (PlannedShift $record) => $record->status === 'request_change')
                    ->action(function (PlannedShift $record) {
                        $record->update(['status' => 'confirmed']);
                        Notification::make()->title('Směna potvrzena manažerem')->success()->send();
                    }),
            ]);
    }
}
