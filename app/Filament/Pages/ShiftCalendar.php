<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\ShiftCalendarWidget;
use App\Models\PlannedShift;
use App\Models\ShiftPlan;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ShiftCalendar extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $navigationGroup = 'HR & Provoz';
    protected static ?int $navigationSort = 1;

    protected static string $view = 'filament.pages.shift-calendar';

    public static function getNavigationLabel(): string
    {
        return auth()->user()?->isManager() ? 'Plánování směn' : 'Můj kalendář';
    }

    public function getTitle(): string
    {
        return auth()->user()?->isManager() ? 'Plánování a Organizace Směn' : 'Můj Pracovní Kalendář';
    }

    protected function getHeaderWidgets(): array
    {
        return [
            ShiftCalendarWidget::class,
        ];
    }

    protected function getHeaderActions(): array
    {
        if (!auth()->user()?->isManager()) {
            return [];
        }

        return [
            // PRŮVODCE GENEROVÁNÍM
            Action::make('generate_plan')
                ->label('Generovat sloty')
                ->icon('heroicon-o-sparkles')
                ->color('primary')
                ->form([
                    Forms\Components\Section::make('Nastavení období')
                        ->schema([
                            Forms\Components\TextInput::make('name')
                                ->label('Název plánu')
                                ->default(fn () => 'Plán: Týden ' . now()->addWeek()->weekOfYear)
                                ->required(),
                            Forms\Components\DatePicker::make('start_date')
                                ->label('Od')
                                ->default(now()->addWeek()->startOfWeek())
                                ->required(),
                            Forms\Components\DatePicker::make('end_date')
                                ->label('Do')
                                ->default(now()->addWeek()->endOfWeek())
                                ->required(),
                        ])->columns(3),

                    Forms\Components\Repeater::make('days')
                        ->label('Nastavení dní')
                        ->schema([
                            Forms\Components\Select::make('day_of_week')
                                ->label('Den')
                                ->options([
                                    1 => 'Pondělí',
                                    2 => 'Úterý',
                                    3 => 'Středa',
                                    4 => 'Čtvrtek',
                                    5 => 'Pátek',
                                    6 => 'Sobota',
                                    7 => 'Neděle',
                                ])
                                ->required(),

                            Forms\Components\Repeater::make('slots')
                                ->label('Pozice a časy')
                                ->schema([
                                    Forms\Components\Select::make('role')
                                        ->options([
                                            'kitchen' => 'Kuchyň',
                                            'floor' => 'Plac / Bar',
                                            'support' => 'Pomocný',
                                        ])
                                        ->required(),
                                    Forms\Components\TimePicker::make('start_time')
                                        ->default('08:00')
                                        ->seconds(false)
                                        ->required(),
                                    Forms\Components\TimePicker::make('end_time')
                                        ->default('16:00')
                                        ->seconds(false)
                                        ->required(),
                                    Forms\Components\TextInput::make('count')
                                        ->label('Počet lidí')
                                        ->numeric()
                                        ->default(1)
                                        ->minValue(1)
                                        ->required(),
                                ])
                                ->columns(4)
                        ])
                        ->defaultItems(1)
                        ->createItemButtonLabel('Přidat denní definici'),
                ])
                ->action(function (array $data) {
                    $this->generateShifts($data);
                }),

            // PUBLIKACE
            Action::make('publish_all')
                ->label('Zveřejnit vše (Drafty)')
                ->icon('heroicon-o-megaphone')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Zveřejnit všechny koncepty?')
                ->modalDescription('Všechny směny ve stavu "Draft" budou převedeny na "Nabídka" (Offered). Uživatelé se na ně budou moci hlásit.')
                ->action(function () {
                    // Update: Draft -> Offered (místo Ordered, protože chceme aby se hlásili)
                    // Pokud je tam uživatel, je to Ordered. Pokud není, je to Offered.

                    PlannedShift::where('status', PlannedShift::STATUS_DRAFT)
                        ->whereNotNull('user_id')
                        ->update(['status' => PlannedShift::STATUS_ORDERED]);

                    PlannedShift::where('status', PlannedShift::STATUS_DRAFT)
                        ->whereNull('user_id')
                        ->update(['status' => PlannedShift::STATUS_OFFERED]);

                    Notification::make()->title("Směny zveřejněny")->success()->send();
                    $this->dispatch('filament-fullcalendar:refresh');
                }),
        ];
    }

    protected function generateShifts(array $data)
    {
        DB::transaction(function () use ($data) {
            // 1. Create Plan
            $plan = ShiftPlan::create([
                'name' => $data['name'],
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'],
                'status' => 'draft',
            ]);

            $start = Carbon::parse($data['start_date']);
            $end = Carbon::parse($data['end_date']);

            // 2. Iterate days in range
            for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
                // Find config for this day of week (1 = Mon, 7 = Sun)
                // Carbon dayOfWeekIso: 1 (Mon) - 7 (Sun)
                $dayConfig = collect($data['days'])->firstWhere('day_of_week', $date->dayOfWeekIso);

                if ($dayConfig) {
                    foreach ($dayConfig['slots'] as $slot) {
                        $count = (int) $slot['count'];
                        for ($i = 0; $i < $count; $i++) {
                            PlannedShift::create([
                                'shift_plan_id' => $plan->id,
                                'start_at' => $date->copy()->setTimeFromTimeString($slot['start_time']),
                                'end_at' => $date->copy()->setTimeFromTimeString($slot['end_time']),
                                'shift_role' => $slot['role'],
                                'status' => PlannedShift::STATUS_DRAFT,
                                'user_id' => null, // Open slot
                            ]);
                        }
                    }
                }
            }
        });

        Notification::make()->title('Plán vygenerován')->success()->send();
        $this->dispatch('filament-fullcalendar:refresh');
    }
}
