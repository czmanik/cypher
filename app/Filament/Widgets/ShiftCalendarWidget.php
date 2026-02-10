<?php

namespace App\Filament\Widgets;

use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;
use App\Models\PlannedShift;
use App\Models\WorkShift;
use App\Models\Reservation;
use App\Models\User;
use Filament\Forms;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Filament\Resources\WorkShiftResource;
use App\Filament\Resources\ReservationResource;

class ShiftCalendarWidget extends FullCalendarWidget
{
    // Ikona a pořadí v menu (pokud bys to chtěl jako stránku, ale teď to bude widget na dashboardu)
    protected static ?int $sort = 5;
    protected int | string | array $columnSpan = 'full';
    protected static ?string $heading = 'Plánovač Směn a Rezervace';

    /**
     * Zde načítáme události (směny) z databáze do kalendáře
     */
    public function fetchEvents(array $fetchInfo): array
    {
        $events = [];

        // 1. Planned Shifts
        $plannedShifts = PlannedShift::query()
            ->where('start_at', '>=', $fetchInfo['start'])
            ->where('end_at', '<=', $fetchInfo['end'])
            ->with('user')
            ->get()
            ->map(function (PlannedShift $shift) {
                return [
                    'id'    => $shift->id,
                    'title' => 'Plán: ' . $shift->user->name . ' (' . ($shift->shift_role ?? $shift->user->employee_type) . ')',
                    'start' => $shift->start_at,
                    'end'   => $shift->end_at,
                    'color' => $shift->is_published ? ($shift->color ?? '#3788d8') : '#9ca3af', // Šedá pro koncepty
                    'editable' => true,
                    // Další data pro editaci
                    'extendedProps' => [
                        'user_id' => $shift->user_id,
                        'description' => $shift->note,
                        'type' => 'planned_shift',
                    ],
                ];
            });

        $events = array_merge($events, $plannedShifts->toArray());

        // 2. Reservations (Visible to everyone)
        $reservations = Reservation::query()
            ->where('reservation_time', '>=', $fetchInfo['start'])
            ->where('reservation_time', '<=', $fetchInfo['end'])
            ->whereIn('status', ['pending', 'confirmed'])
            ->get()
            ->map(function (Reservation $reservation) {
                $endTime = $reservation->reservation_time->copy()->addMinutes($reservation->duration_minutes);

                $color = match($reservation->status) {
                    'confirmed' => '#10b981', // green
                    'pending' => '#f59e0b',   // orange
                    default => '#6b7280',
                };

                return [
                    'id' => 'res_' . $reservation->id,
                    'title' => "Rez: {$reservation->customer_name} ({$reservation->guests_count})",
                    'start' => $reservation->reservation_time,
                    'end' => $endTime,
                    'color' => $color,
                    'editable' => false,
                    'url' => auth()->user()?->is_manager ? ReservationResource::getUrl('edit', ['record' => $reservation]) : null,
                    'extendedProps' => [
                        'type' => 'reservation',
                    ],
                ];
            });

        $events = array_merge($events, $reservations->toArray());

        // 3. Past Work Shifts (Actual)
        $query = WorkShift::query()
            ->where('start_at', '>=', $fetchInfo['start'])
            ->where('end_at', '<=', $fetchInfo['end']);

        // Employees see only their own shifts
        if (! auth()->user()?->is_manager) {
            $query->where('user_id', auth()->id());
        }

        $workShifts = $query->with('user')->get()->map(function (WorkShift $shift) {
            $isPaid = $shift->status === 'paid';

            return [
                'id' => 'work_' . $shift->id,
                'title' => "Směna: " . $shift->user->name,
                'start' => $shift->start_at,
                'end' => $shift->end_at,
                'backgroundColor' => $isPaid ? '#f3f4f6' : '#e5e7eb', // Světle šedá
                'borderColor' => $isPaid ? '#d1d5db' : '#9ca3af',
                'textColor' => $isPaid ? '#9ca3af' : '#111827',
                'editable' => false,
                'classNames' => $isPaid ? ['opacity-75', 'line-through'] : [],
                'url' => WorkShiftResource::getUrl('view', ['record' => $shift]),
                'extendedProps' => [
                    'type' => 'work_shift',
                ],
            ];
        });

        $events = array_merge($events, $workShifts->toArray());

        return $events;
    }

    /**
     * Formulář, který vyskočí, když klikneš do kalendáře
     */
    public function getFormSchema(): array
    {
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
                    ->minutesStep(15), // Skákat po 15 min
                
                Forms\Components\DateTimePicker::make('end_at')
                    ->label('Konec')
                    ->required()
                    ->seconds(false)
                    ->minutesStep(15),
            ]),

            Forms\Components\Select::make('shift_role')
                ->label('Pozice pro tuto směnu')
                ->options([
                    'manager' => 'Management',
                    'kitchen' => 'Kuchyň',
                    'floor' => 'Plac / Bar',
                    'support' => 'Pomocný',
                ])
                ->helperText('Nechte prázdné, pokud platí výchozí pozice zaměstnance.'),

            Forms\Components\Textarea::make('note')
                ->label('Poznámka'),

            Forms\Components\Toggle::make('is_published')
                ->label('Zveřejnit směnu')
                ->default(true)
                ->onColor('success')
                ->offColor('gray'),
        ];
    }

    // Co se stane při odeslání formuláře (Vytvoření / Editace)
    // Plugin to řeší automaticky, pokud definujeme model!
    // Ale musíme mu říct, jak mapovat data.
    
    protected function headerActions(): array
    {
        return [
            // Tlačítko pro vytvoření směny (kdyby někdo nechtěl klikat do kalendáře)
            // FullCalendar to má vestavěné v klikání do dnů
        ];
    }
    
    // Povolení editace po kliknutí na událost
    protected function modalActions(): array
    {
        return [
            \Filament\Actions\EditAction::make()
                ->mountUsing(
                    function (PlannedShift $record, Forms\Form $form, array $arguments) {
                        $form->fill([
                            'user_id' => $record->user_id,
                            'start_at' => $record->start_at,
                            'end_at' => $record->end_at,
                            'shift_role' => $record->shift_role,
                            'note' => $record->note,
                            'is_published' => $record->is_published,
                        ]);
                    }
                ),
            \Filament\Actions\DeleteAction::make(),
        ];
    }

    // Důležité: Řekneme widgetu, s jakým modelem pracuje
    public function getModel(): string
    {
        return PlannedShift::class;
    }
}