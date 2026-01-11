<?php

namespace App\Filament\Widgets;

use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;
use App\Models\PlannedShift;
use App\Models\User;
use Filament\Forms;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Filament\Notifications\Notification;

class ShiftCalendarWidget extends FullCalendarWidget
{
    protected static ?int $sort = 5;
    protected int | string | array $columnSpan = 'full';

    // Heading is handled by the Page title now.
    public function getHeading(): string|null
    {
        return null;
    }

    public function fetchEvents(array $fetchInfo): array
    {
        $user = auth()->user();
        $isManager = $user->isManager();

        $query = PlannedShift::query()
            ->where('start_at', '>=', $fetchInfo['start'])
            ->where('end_at', '<=', $fetchInfo['end'])
            ->with('user');

        if (!$isManager) {
            // Zaměstnanec vidí jen své směny, které nejsou draft
            $query->where('user_id', $user->id)
                  ->where('status', '!=', PlannedShift::STATUS_DRAFT);
        }

        return $query->get()
            ->map(function (PlannedShift $shift) use ($isManager) {
                // Barvy podle statusu
                $color = match ($shift->status) {
                    PlannedShift::STATUS_DRAFT => '#9ca3af', // Gray
                    PlannedShift::STATUS_ORDERED => '#3b82f6', // Blue
                    PlannedShift::STATUS_OFFERED => '#eab308', // Yellow
                    PlannedShift::STATUS_REQUESTED => '#a855f7', // Purple
                    PlannedShift::STATUS_CONFIRMED => '#22c55e', // Green
                    PlannedShift::STATUS_REJECTED => '#ef4444', // Red
                    default => '#3788d8',
                };

                return [
                    'id'    => $shift->id,
                    'title' => $this->getEventTitle($shift, $isManager),
                    'start' => $shift->start_at,
                    'end'   => $shift->end_at,
                    'color' => $color,
                    'extendedProps' => [
                        'user_id' => $shift->user_id,
                        'description' => $shift->note,
                        'status' => $shift->status,
                    ],
                    // Zaměstnanec nemůže hýbat se směnou, pokud to není jeho request (a i tak radši přes formulář)
                    'editable' => $isManager,
                ];
            })
            ->toArray();
    }

    protected function getEventTitle(PlannedShift $shift, bool $isManager): string
    {
        if ($isManager) {
            $statusPrefix = match($shift->status) {
                PlannedShift::STATUS_DRAFT => '[DRAFT] ',
                PlannedShift::STATUS_REQUESTED => '[ŽÁDOST] ',
                PlannedShift::STATUS_OFFERED => '[NABÍDKA] ',
                default => '',
            };
            return $statusPrefix . $shift->user->name . ' (' . ($shift->shift_role ?? $shift->user->employee_type) . ')';
        }

        // Pro zaměstnance
        return match($shift->status) {
            PlannedShift::STATUS_REQUESTED => 'Moje žádost',
            PlannedShift::STATUS_OFFERED => 'Nabídka směny (Klikni pro přijetí)',
            PlannedShift::STATUS_ORDERED => 'Směna: ' . ($shift->shift_role ?? 'Standard'),
            PlannedShift::STATUS_CONFIRMED => 'Potvrzeno',
            default => 'Směna',
        };
    }

    public function getFormSchema(): array
    {
        $isManager = auth()->user()->isManager();

        // SCHÉMA PRO MANAGERA
        if ($isManager) {
            return [
                Forms\Components\Select::make('user_id')
                    ->label('Zaměstnanec')
                    ->options(User::where('is_active', true)->pluck('name', 'id'))
                    ->required()
                    ->searchable(),

                Forms\Components\Grid::make(2)->schema([
                    Forms\Components\DateTimePicker::make('start_at')
                        ->label('Začátek')
                        ->required()
                        ->seconds(false)
                        ->minutesStep(15),

                    Forms\Components\DateTimePicker::make('end_at')
                        ->label('Konec')
                        ->required()
                        ->seconds(false)
                        ->minutesStep(15),
                ]),

                Forms\Components\Select::make('status')
                    ->label('Stav')
                    ->options([
                        PlannedShift::STATUS_DRAFT => 'Draft (Skryté)',
                        PlannedShift::STATUS_ORDERED => 'Nařízeno (Viditelné)',
                        PlannedShift::STATUS_OFFERED => 'Nabídnout (Čeká na schválení)',
                        PlannedShift::STATUS_CONFIRMED => 'Potvrzeno',
                    ])
                    ->default(PlannedShift::STATUS_ORDERED)
                    ->required(),

                Forms\Components\Select::make('shift_role')
                    ->label('Pozice')
                    ->options([
                        'manager' => 'Management',
                        'kitchen' => 'Kuchyň',
                        'floor' => 'Plac / Bar',
                        'support' => 'Pomocný',
                    ]),

                Forms\Components\Textarea::make('note')
                    ->label('Poznámka'),
            ];
        }

        // SCHÉMA PRO ZAMĚSTNANCE (Žádost o dostupnost)
        return [
            Forms\Components\Hidden::make('user_id')
                ->default(auth()->id()),

            Forms\Components\Hidden::make('status')
                ->default(PlannedShift::STATUS_REQUESTED),

            Forms\Components\DateTimePicker::make('start_at')
                ->label('Jsem dostupný od')
                ->required()
                ->seconds(false)
                ->minutesStep(30),

            Forms\Components\DateTimePicker::make('end_at')
                ->label('do')
                ->required()
                ->seconds(false)
                ->minutesStep(30),

            Forms\Components\Textarea::make('note')
                ->label('Poznámka (volitelné)'),
        ];
    }

    protected function modalActions(): array
    {
        $actions = [];
        $user = auth()->user();

        if ($user->isManager()) {
            $actions[] = EditAction::make()->mountUsing(fn ($record, $form) => $form->fill([
                'user_id' => $record->user_id,
                'start_at' => $record->start_at,
                'end_at' => $record->end_at,
                'status' => $record->status,
                'shift_role' => $record->shift_role,
                'note' => $record->note,
            ]));
            $actions[] = DeleteAction::make();
        } else {
            // ZAMĚSTNANEC
            // 1. Akce pro přijetí nabídky
            $actions[] = Action::make('accept_offer')
                ->label('Přijmout směnu')
                ->color('success')
                ->visible(fn (PlannedShift $record) => $record->status === PlannedShift::STATUS_OFFERED && $record->user_id === $user->id)
                ->action(function (PlannedShift $record) {
                    $record->update(['status' => PlannedShift::STATUS_CONFIRMED]);
                    Notification::make()->title('Směna přijata')->success()->send();
                });

            // 2. Akce pro odmítnutí nabídky
            $actions[] = Action::make('reject_offer')
                ->label('Odmítnout')
                ->color('danger')
                ->visible(fn (PlannedShift $record) => $record->status === PlannedShift::STATUS_OFFERED && $record->user_id === $user->id)
                ->requiresConfirmation()
                ->action(function (PlannedShift $record) {
                    $record->update(['status' => PlannedShift::STATUS_REJECTED]);
                    Notification::make()->title('Směna odmítnuta')->success()->send();
                });

            // 3. Editace vlastní žádosti (pokud ještě nebyla schválena/zamítnuta)
            $actions[] = EditAction::make()
                ->label('Upravit žádost')
                ->visible(fn (PlannedShift $record) => $record->status === PlannedShift::STATUS_REQUESTED && $record->user_id === $user->id)
                ->mountUsing(fn ($record, $form) => $form->fill([
                    'start_at' => $record->start_at,
                    'end_at' => $record->end_at,
                    'note' => $record->note,
                ]));

            // 4. Smazání vlastní žádosti
             $actions[] = DeleteAction::make()
                ->label('Zrušit žádost')
                ->visible(fn (PlannedShift $record) => $record->status === PlannedShift::STATUS_REQUESTED && $record->user_id === $user->id);
        }

        return $actions;
    }

    public function getModel(): string
    {
        return PlannedShift::class;
    }

    // Potřebné pro refresh eventů
    public function refreshEvents(): void
    {
        $this->dispatch('filament-fullcalendar:refresh');
    }
}
