<?php

namespace App\Filament\Widgets;

use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;
use App\Models\PlannedShift;
use App\Models\User;
use Filament\Forms;
use Filament\Actions;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Filament\Notifications\Notification;

class ShiftCalendarWidget extends FullCalendarWidget
{
    protected static ?int $sort = 5;
    protected int | string | array $columnSpan = 'full';
    protected static ?string $heading = 'Plánovač Směn';

    // Property for filtering
    public ?string $filterEmployeeType = null;

    /**
     * Define colors for statuses
     */
    protected const COLOR_DRAFT = '#9ca3af'; // Gray
    protected const COLOR_PENDING = '#3b82f6'; // Blue
    protected const COLOR_CONFIRMED = '#22c55e'; // Green
    protected const COLOR_CHANGE_REQUEST = '#f97316'; // Orange/Red

    public function fetchEvents(array $fetchInfo): array
    {
        $query = PlannedShift::query()
            ->where('start_at', '>=', $fetchInfo['start'])
            ->where('end_at', '<=', $fetchInfo['end'])
            ->with('user');

        if ($this->filterEmployeeType && $this->filterEmployeeType !== 'all') {
            $query->whereHas('user', function ($q) {
                $q->where('employee_type', $this->filterEmployeeType);
            });
        }

        return $query->get()
            ->map(function (PlannedShift $shift) {
                // Determine color based on status and publication
                $color = self::COLOR_DRAFT;
                if ($shift->is_published) {
                    $color = match ($shift->status) {
                        'confirmed' => self::COLOR_CONFIRMED,
                        'request_change' => self::COLOR_CHANGE_REQUEST,
                        default => self::COLOR_PENDING,
                    };
                }

                return [
                    'id'    => $shift->id,
                    'title' => $shift->user->name . ' (' . ($shift->shift_role ?? $shift->user->employee_type) . ')',
                    'start' => $shift->start_at,
                    'end'   => $shift->end_at,
                    'backgroundColor' => $color,
                    'borderColor' => $color,
                    'extendedProps' => [
                        'user_id' => $shift->user_id,
                        'description' => $shift->note,
                        'status' => $shift->status,
                    ],
                ];
            })
            ->toArray();
    }

    public function getFormSchema(): array
    {
        return [
            // Create Mode: Multi-select
            Forms\Components\Select::make('user_ids')
                ->label('Zaměstnanci (Hromadně)')
                ->options(User::where('is_active', true)->pluck('name', 'id'))
                ->multiple()
                ->required()
                ->searchable()
                ->hidden(fn ($operation) => $operation === 'edit'), // Only for create

            // Edit Mode: Single-select (readonly ideally, or changeable)
            Forms\Components\Select::make('user_id')
                ->label('Zaměstnanec')
                ->options(User::where('is_active', true)->pluck('name', 'id'))
                ->required()
                ->hidden(fn ($operation) => $operation === 'create') // Only for edit
                ->disabled(),

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

            Forms\Components\Select::make('shift_role')
                ->label('Pozice pro tuto směnu')
                ->options([
                    'manager' => 'Management',
                    'kitchen' => 'Kuchyň',
                    'floor' => 'Plac / Bar',
                    'support' => 'Pomocný',
                ])
                ->helperText('Nechte prázdné pro výchozí pozici.'),

            Forms\Components\Textarea::make('note')
                ->label('Poznámka'),

            Forms\Components\Toggle::make('is_published')
                ->label('Zveřejnit ihned')
                ->default(true)
                ->onColor('success')
                ->offColor('gray'),
        ];
    }

    /**
     * Override creation to handle multiple users
     */
    public function createEvent(array $data): void
    {
        $userIds = $data['user_ids'] ?? [];

        if (empty($userIds)) {
             return;
        }

        foreach ($userIds as $userId) {
            PlannedShift::create([
                'user_id' => $userId,
                'start_at' => $data['start_at'],
                'end_at' => $data['end_at'],
                'shift_role' => $data['shift_role'],
                'note' => $data['note'],
                'is_published' => $data['is_published'] ?? false,
                'status' => 'pending',
            ]);
        }

        Notification::make()
            ->title('Směny vytvořeny')
            ->success()
            ->send();

        $this->refreshEvents();
    }

    /**
     * Header Actions (Filters, Publish)
     */
    protected function headerActions(): array
    {
        return [
            // FILTER ACTION
            Actions\Action::make('filter')
                ->label('Filtrovat zobrazení')
                ->icon('heroicon-o-funnel')
                ->form([
                    Forms\Components\Select::make('type')
                        ->label('Oddělení')
                        ->options([
                            'all' => 'Všechna oddělení',
                            'kitchen' => 'Kuchyň',
                            'floor' => 'Plac / Bar',
                            'manager' => 'Management',
                        ])
                        ->default($this->filterEmployeeType ?? 'all')
                        ->required(),
                ])
                ->action(function (array $data) {
                    $this->filterEmployeeType = $data['type'];
                    $this->refreshEvents();
                }),

            // PUBLISH ACTION
            Actions\Action::make('publish_month')
                ->label('Zveřejnit měsíc')
                ->color('success')
                ->requiresConfirmation()
                ->form([
                    Forms\Components\DatePicker::make('month_start')
                        ->label('Vyberte měsíc')
                        ->default(now())
                        ->required(),
                ])
                ->action(function (array $data) {
                    $start = Carbon::parse($data['month_start'])->startOfMonth();
                    $end = Carbon::parse($data['month_start'])->endOfMonth();

                    PlannedShift::whereBetween('start_at', [$start, $end])
                        ->where('is_published', false)
                        ->update(['is_published' => true]);

                    Notification::make()->title('Směny zveřejněny')->success()->send();
                    $this->refreshEvents();
                }),

            Actions\CreateAction::make()
                ->label('Nová směna')
                ->form($this->getFormSchema()) // Correctly reuse the schema!
                ->mountUsing(fn (Forms\Form $form) => $form->fill([
                    'start_at' => now()->setTime(8, 0),
                    'end_at' => now()->setTime(16, 0),
                    'is_published' => true,
                ]))
                ->using(function (array $data, string $model) {
                    $this->createEvent($data);
                    return new PlannedShift();
                }),
        ];
    }
    
    protected function modalActions(): array
    {
        return [
            Actions\EditAction::make()
                ->mountUsing(
                    function (PlannedShift $record, Forms\Form $form) {
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
            Actions\DeleteAction::make(),
        ];
    }

    public function getModel(): string
    {
        return PlannedShift::class;
    }
}
