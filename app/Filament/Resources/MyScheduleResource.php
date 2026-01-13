<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MyScheduleResource\Pages;
use App\Models\PlannedShift;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Notifications\Notification;

class MyScheduleResource extends Resource
{
    // Point to the existing PlannedShift model
    protected static ?string $model = PlannedShift::class;

    protected static ?string $navigationLabel = 'Moje Směny';
    protected static ?string $pluralModelLabel = 'Moje Směny';
    protected static ?string $modelLabel = 'Směna';
    protected static ?string $navigationGroup = 'Moje Práce';
    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?int $navigationSort = 1;

    // Only show queries for the logged-in user
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('user_id', auth()->id())
            ->where('is_published', true) // Only see published shifts
            ->orderBy('start_at');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Read-only info for the "Edit" action if we were to use it,
                // but we primarily want Actions for Accept/Reject.
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('start_at')
                    ->label('Den')
                    ->date('l d.m.Y') // Den v týdnu + datum
                    ->sortable(),

                Tables\Columns\TextColumn::make('time_range')
                    ->label('Čas')
                    ->state(fn (PlannedShift $record) => $record->start_at->format('H:i') . ' - ' . $record->end_at->format('H:i')),

                Tables\Columns\TextColumn::make('shift_role')
                    ->label('Pozice')
                    ->formatStateUsing(fn ($state) => match($state) {
                        'kitchen' => 'Kuchyň',
                        'floor' => 'Plac',
                        'manager' => 'Management',
                        'support' => 'Pomoc',
                        default => $state ?? 'Standardní'
                    }),

                Tables\Columns\TextColumn::make('note')
                    ->label('Poznámka Manažera')
                    ->limit(30),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Stav')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'confirmed',
                        'danger' => 'request_change',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'Čeká na potvrzení',
                        'confirmed' => 'Potvrzeno',
                        'request_change' => 'Žádost o změnu',
                        default => $state,
                    }),
            ])
            ->filters([
                Tables\Filters\Filter::make('upcoming')
                    ->label('Budoucí směny')
                    ->query(fn (Builder $query) => $query->where('start_at', '>=', now()->startOfDay()))
                    ->default(),
            ])
            ->actions([
                // ACCEPT ACTION
                Tables\Actions\Action::make('confirm')
                    ->label('Potvrdit')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(fn (PlannedShift $record) => $record->status === 'pending')
                    ->action(function (PlannedShift $record) {
                        $record->update(['status' => 'confirmed']);
                        Notification::make()->title('Směna potvrzena')->success()->send();
                    }),

                // REQUEST CHANGE ACTION
                Tables\Actions\Action::make('request_change')
                    ->label('Nemůžu')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->form([
                        Forms\Components\Textarea::make('employee_comment')
                            ->label('Důvod / Požadavek')
                            ->required(),
                    ])
                    ->action(function (PlannedShift $record, array $data) {
                        $record->update([
                            'status' => 'request_change',
                            'employee_comment' => $data['employee_comment'],
                        ]);
                        Notification::make()->title('Žádost o změnu odeslána')->success()->send();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkAction::make('confirm_all')
                    ->label('Potvrdit vybrané')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->action(function ($records) {
                        foreach ($records as $record) {
                            if ($record->status === 'pending') {
                                $record->update(['status' => 'confirmed']);
                            }
                        }
                        Notification::make()->title('Směny hromadně potvrzeny')->success()->send();
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageMySchedules::route('/'),
        ];
    }
}
