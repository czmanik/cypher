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

    /**
     * Fetch events for the calendar.
     * Note: Type hint for $fetchInfo removed to prevent Livewire hydration issues.
     */
    public function fetchEvents($fetchInfo): array
    {
        $user = auth()->user();
        $isManager = $user->isManager();

        $query = PlannedShift::query()
            ->where('start_at', '>=', $fetchInfo['start'])
            ->where('end_at', '<=', $fetchInfo['end'])
            ->with('user');

        if (!$isManager) {
            // Zaměstnanec vidí jen své směny, které nejsou draft...
            // A TAKÉ vidí volné směny (user_id IS NULL) s statusem OFFERED
            $query->where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->where('status', '!=', PlannedShift::STATUS_DRAFT);
            })->orWhere(function ($q) {
                $q->whereNull('user_id')
                  ->where('status', PlannedShift::STATUS_OFFERED);
            });
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
        // Název role/pozice
        $roleLabel = match($shift->shift_role) {
            'manager' => 'Management',
            'kitchen' => 'Kuchyň',
            'floor' => 'Plac / Bar',
            'support' => 'Pomoc',
            default => $shift->shift_role ?? 'Směna',
        };

        if ($isManager) {
            $statusPrefix = match($shift->status) {
                PlannedShift::STATUS_DRAFT => '[DRAFT] ',
                PlannedShift::STATUS_REQUESTED => '[ŽÁDOST] ',
                PlannedShift::STATUS_OFFERED => '[NABÍDKA] ',
                default => '',
            };

            // Pokud je slot volný (nemá usera)
            if (!$shift->user_id) {
                return $statusPrefix . 'Volno: ' . $roleLabel;
            }

            return $statusPrefix . $shift->user->name . ' (' . ($shift->shift_role ?? $shift->user->employee_type) . ')';
        }

        // Pro zaměstnance
        // Pokud je slot volný (nabídka k převzetí)
        if (!$shift->user_id && $shift->status === PlannedShift::STATUS_OFFERED) {
            return 'VOLNO: ' . $roleLabel . ' (Klikni)';
        }

        return match($shift->status) {
            PlannedShift::STATUS_REQUESTED => 'Moje žádost',
            PlannedShift::STATUS_OFFERED => 'Nabídka: ' . $roleLabel, // (Specificky pro user assigned offer)
            PlannedShift::STATUS_ORDERED => 'Směna: ' . $roleLabel,
            PlannedShift::STATUS_CONFIRMED => 'Potvrzeno: ' . $roleLabel,
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
                    ->helperText('Nechte prázdné pro vytvoření "Volné směny" k nabídnutí.')
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
            // 1. Akce pro přijetí nabídky (Pokud je přiřazen přímo mně)
            $actions[] = Action::make('accept_assigned_offer')
                ->label('Potvrdit směnu')
                ->color('success')
                ->visible(fn (PlannedShift $record) => $record->status === PlannedShift::STATUS_OFFERED && $record->user_id === $user->id)
                ->action(function (PlannedShift $record) {
                    $record->update(['status' => PlannedShift::STATUS_CONFIRMED]);
                    Notification::make()->title('Směna potvrzena')->success()->send();
                });

            // 1b. Akce pro PŘEVZETÍ volné směny (Pokud je user_id null)
            $actions[] = Action::make('take_open_shift')
                ->label('Vzít si směnu')
                ->color('success')
                ->visible(fn (PlannedShift $record) => $record->status === PlannedShift::STATUS_OFFERED && $record->user_id === null)
                ->requiresConfirmation()
                ->modalHeading('Vzít si tuto směnu?')
                ->modalDescription(fn (PlannedShift $record) => 'Opravdu si chcete zapsat směnu: ' . $record->start_at->format('d.m. H:i') . '?')
                ->action(function (PlannedShift $record) use ($user) {
                    $record->update([
                        'user_id' => $user->id,
                        'status' => PlannedShift::STATUS_CONFIRMED, // Rovnou potvrdíme, když si ji vzal sám
                    ]);
                    Notification::make()->title('Směna zapsána')->success()->send();
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

    public function refreshCalendar(): void
    {
        $this->dispatch('filament-fullcalendar:refresh');
    }

    protected $listeners = [
        'refresh-calendar' => 'refreshCalendar',
    ];
}
