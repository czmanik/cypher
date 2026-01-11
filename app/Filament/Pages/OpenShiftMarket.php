<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use App\Models\PlannedShift;
use Illuminate\Database\Eloquent\Builder;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use App\Models\ShiftAuditLog;

class OpenShiftMarket extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    protected static ?string $navigationLabel = 'Tržiště směn';
    protected static ?string $navigationGroup = 'Moje Práce';
    protected static ?string $title = 'Tržiště volných směn';
    protected static ?int $navigationSort = 3;

    protected static string $view = 'filament.pages.open-shift-market';

    // FIX: Allow access for all authenticated users (employees)
    // By default, Filament checks permissions. If no policy, it might restrict.
    // Assuming 'viewAny' is handled by Panel, but explicit access might help.

    public static function canAccess(): bool
    {
        return Auth::check();
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                PlannedShift::query()
                    ->whereNull('user_id') // Only unassigned shifts
                    ->where('is_published', true)
                    ->where('start_at', '>=', now()) // Only future
                    ->where(function (Builder $query) {
                        $user = Auth::user();

                        // Managers see everything
                        if ($user->is_manager) return;

                        // Employee sees shifts that match ONE of their roles OR open to all
                        $userTypes = $user->employee_type ?? [];

                        $query->whereNull('shift_role')
                              ->orWhereIn('shift_role', $userTypes);
                    })
                    ->orderBy('start_at')
            )
            ->columns([
                Tables\Columns\TextColumn::make('start_at')
                    ->label('Datum')
                    ->date('l d.m.Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('time_range')
                    ->label('Čas')
                    ->state(fn (PlannedShift $record) => $record->start_at->format('H:i') . ' - ' . $record->end_at->format('H:i')),

                Tables\Columns\TextColumn::make('shift_role')
                    ->label('Role')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state ? ucfirst($state) : 'Všichni'),

                Tables\Columns\TextColumn::make('bonus')
                    ->label('Bonus')
                    ->money('CZK')
                    ->weight('bold')
                    ->color('success')
                    ->visible(fn ($record) => $record && $record->bonus > 0),

                Tables\Columns\TextColumn::make('note')
                    ->label('Poznámka')
                    ->limit(50),
            ])
            ->actions([
                Tables\Actions\Action::make('claim')
                    ->label('Mám zájem')
                    ->icon('heroicon-o-hand-raised')
                    ->button()
                    ->color('primary')
                    ->requiresConfirmation()
                    ->modalHeading('Potvrdit zájem o směnu')
                    ->modalDescription('Opravdu se chcete přihlásit na tuto směnu? Manažer bude muset vaši žádost schválit.')
                    ->action(function (PlannedShift $record) {
                        // Check if still available (race condition)
                        if ($record->user_id) {
                            Notification::make()->title('Směna již byla obsazena.')->danger()->send();
                            return;
                        }

                        // Assign to user and set status pending
                        $record->update([
                            'user_id' => Auth::id(),
                            'status' => 'pending', // Waiting for manager approval
                        ]);

                        // Log
                        ShiftAuditLog::create([
                            'planned_shift_id' => $record->id,
                            'user_id' => Auth::id(),
                            'action' => 'claimed',
                            'payload' => ['previous_status' => 'open'],
                        ]);

                        Notification::make()->title('Žádost odeslána. Čekejte na schválení.')->success()->send();
                    }),
            ])
            ->emptyStateHeading('Žádné volné směny')
            ->emptyStateDescription('Momentálně nejsou k dispozici žádné volné směny pro vaši pozici.');
    }
}
