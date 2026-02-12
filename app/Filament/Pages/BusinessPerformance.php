<?php

namespace App\Filament\Pages;

use App\Models\WorkShift;
use App\Services\StoryousService;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Cache;

class BusinessPerformance extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-presentation-chart-line';
    protected static ?string $navigationGroup = 'HR & Provoz';
    protected static ?string $navigationLabel = 'Výkon podniku';
    protected static ?string $title = 'Výkon podniku';
    protected static ?int $navigationSort = 2;

    protected static string $view = 'filament.pages.business-performance';

    public $date;
    public $revenue = 0.0;
    public $laborCosts = 0.0;
    public $profit = 0.0;
    public $shifts = [];

    public function mount()
    {
        $this->date = request()->query('date', now()->format('Y-m-d'));
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
                ->color('primary')
                ->action(function () {
                    // Force refresh Storyous cache for the selected date
                    $cacheKey = 'storyous_bills_' . Carbon::parse($this->date)->format('Y-m-d');
                    Cache::forget($cacheKey);
                    $this->loadData();
                }),
        ];
    }

    public function loadData()
    {
        $date = Carbon::parse($this->date);

        // 1. Revenue
        /** @var StoryousService $service */
        $service = app(StoryousService::class);
        $this->revenue = $service->getRevenueForDate($date);

        // 2. Labor Costs
        // Query shifts starting on this date
        $shifts = WorkShift::with('user')
            ->whereDate('start_at', $date)
            ->get();

        $this->laborCosts = 0.0;
        $this->shifts = [];

        foreach ($shifts as $shift) {
            $cost = 0.0;

            // If shift is active (end_at is null)
            if (!$shift->end_at) {
                // Calculate current duration
                $now = now();
                // If the shift started today, calculate up to now.
                // If the shift started yesterday but viewing yesterday, calculate up to end of day?
                // Simplified: use now() if viewing today, or end of day if viewing past?
                // Or simply: if active, use duration so far.

                // If viewing a past date and shift is somehow still active (forgot to close), calculate until midnight?
                // But let's assume active shifts are relevant for "today".

                $hourlyRate = $shift->user->hourly_rate ?? 0;

                // Check salary type (fixed vs hourly)
                if (($shift->user->salary_type ?? 'hourly') === 'fixed') {
                    // Fixed salary per shift - entire cost counts immediately (or we could split it, but worst case is full cost)
                    $cost = $hourlyRate;
                } else {
                    // Hourly salary - calculate based on duration so far
                    $durationHours = $shift->start_at->diffInHours($now) + ($shift->start_at->diffInMinutes($now) % 60) / 60;
                    $cost = $durationHours * $hourlyRate;
                }
            } else {
                // Finished shift uses calculated_wage + bonus - penalty
                // Wait, user request: "spočítat náklady na zaměstnance"
                // Usually Cost = Wage + Employer Taxes etc. But here simple: Wage + Bonus.
                // Penalty? Usually reduces payout but maybe not cost if damage? Let's assume Cost = Wage + Bonus.
                $cost = ($shift->calculated_wage ?? 0) + ($shift->bonus ?? 0);
            }

            $this->laborCosts += $cost;

            $this->shifts[] = [
                'user_name' => $shift->user->name ?? 'Unknown',
                'start_at' => $shift->start_at->format('H:i'),
                'end_at' => $shift->end_at ? $shift->end_at->format('H:i') : 'Active',
                'status' => $shift->status,
                'cost' => $cost,
            ];
        }

        // 3. Profit
        $this->profit = $this->revenue - $this->laborCosts;
    }
}
