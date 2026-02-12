<?php

namespace App\Filament\Pages;

use App\Models\WorkShift;
use App\Services\StoryousService;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Pages\Page;
use Illuminate\Support\Collection;

class BusinessPerformance extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-presentation-chart-line';
    protected static ?string $navigationLabel = 'Výkon podniku';
    protected static ?string $title = 'Výkon podniku';
    protected static ?string $navigationGroup = 'HR & Provoz';
    protected static ?int $navigationSort = 2;

    protected static string $view = 'filament.pages.business-performance';

    public $date;
    public $revenue = 0.0;
    public $laborCost = 0.0;
    public $profit = 0.0;
    public $shifts = [];

    public function mount()
    {
        $this->date = now()->format('Y-m-d');
        $this->loadData();
    }

    public static function canAccess(): bool
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        return $user && ($user->is_manager || $user->is_admin);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('previousDay')
                ->label('Předchozí den')
                ->icon('heroicon-o-chevron-left')
                ->color('gray')
                ->action(function () {
                    $newDate = Carbon::parse($this->date)->subDay();
                    $this->date = $newDate->format('Y-m-d');
                    $this->loadData();
                }),

            Action::make('nextDay')
                ->label('Následující den')
                ->icon('heroicon-o-chevron-right')
                ->color('gray')
                ->action(function () {
                    $newDate = Carbon::parse($this->date)->addDay();
                    $this->date = $newDate->format('Y-m-d');
                    $this->loadData();
                }),

            Action::make('selectDate')
                ->label('Změnit datum')
                ->icon('heroicon-o-calendar')
                ->form([
                    DatePicker::make('date_picker')
                        ->label('Datum')
                        ->required()
                        ->default(fn () => $this->date),
                ])
                ->action(function (array $data) {
                    $this->date = Carbon::parse($data['date_picker'])->format('Y-m-d');
                    $this->loadData();
                }),

            Action::make('refresh')
                ->label('Aktualizovat')
                ->icon('heroicon-o-arrow-path')
                ->action(fn () => $this->loadData()),
        ];
    }

    public function loadData()
    {
        $carbonDate = Carbon::parse($this->date);

        // 1. Storyous Revenue
        /** @var StoryousService $service */
        $service = app(StoryousService::class);
        $this->revenue = $service->getRevenueForDate($carbonDate);

        // 2. Labor Costs
        $this->calculateLaborCosts($carbonDate);

        // 3. Profit
        $this->profit = $this->revenue - $this->laborCost;
    }

    protected function calculateLaborCosts(Carbon $date)
    {
        // Najdeme směny, které začaly v daný den
        // (nebo probíhají v daný den? Pro zjednodušení bereme start_at v ten den,
        // což je standard pro denní uzávěrky v gastru)
        $shifts = WorkShift::with('user')
            ->whereDate('start_at', $date)
            ->get();

        $totalCost = 0.0;
        $shiftData = [];

        foreach ($shifts as $shift) {
            $cost = 0.0;
            $status = 'Uzavřeno';
            $hours = 0.0;

            if ($shift->end_at) {
                // Uzavřená směna - bereme vypočtenou mzdu
                // (calculated_wage už zohledňuje typ mzdy fix/hodina v modelu WorkShift)
                $cost = (float) $shift->calculated_wage;
                $hours = (float) $shift->total_hours;
            } else {
                // Otevřená směna - musíme dopočítat aktuální náklad
                $status = 'Probíhá';

                // Pokud směna běží, počítáme do "teď" (nebo do konce dne, pokud se díváme do historie?)
                // Pokud se díváme na dnešek, je to "teď".
                // Pokud se díváme na minulost a směna nemá end_at (zapomenutá?), tak je to problém,
                // ale budeme předpokládat "teď" nebo fixně 0 pokud je to divné.
                // Pro "Probíhá" dává smysl počítat jen pokud je to dnešní směna.

                $now = now();
                // Ošetření: pokud je datum v minulosti a směna stále běží, počítáme do teď (dlouhá směna)
                // nebo to ignorujeme? Zde počítáme duration od startu do teď.

                $start = $shift->start_at;
                if ($start) {
                    $minutes = $start->diffInMinutes($now);
                    $hours = round($minutes / 60, 2);

                    $user = $shift->user;
                    if ($user) {
                        if ($user->salary_type === 'fixed') {
                            // Fixní mzda - celá částka ihned (náklad na směnu je fixní)
                            $cost = (float) $user->hourly_rate;
                        } else {
                            // Hodinová mzda * odpracované hodiny
                            $cost = $hours * (float) $user->hourly_rate;
                        }
                    }
                }
            }

            $totalCost += $cost;

            $shiftData[] = [
                'user_name' => $shift->user->name ?? 'Neznámý',
                'status' => $status,
                'start' => $shift->start_at->format('H:i'),
                'end' => $shift->end_at ? $shift->end_at->format('H:i') : '-',
                'hours' => $hours,
                'cost' => $cost,
                'salary_type' => ($shift->user->salary_type ?? 'hourly') === 'fixed' ? 'Fixní' : 'Hodinová',
            ];
        }

        $this->laborCost = $totalCost;
        $this->shifts = $shiftData;
    }
}
