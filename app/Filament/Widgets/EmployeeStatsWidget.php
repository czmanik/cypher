<?php

namespace App\Filament\Widgets;

use App\Models\WorkShift;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class EmployeeStatsWidget extends BaseWidget
{
    protected static ?int $sort = 1; // Dej to nahoru

    public static function canView(): bool
    {
        // Vidí to každý (manager i zaměstnanec), ale zobrazíme data pro přihlášeného
        return true;
    }

    protected function getStats(): array
    {
        $user = auth()->user();
        if (!$user) return [];

        // 1. Mzda k vyplacení (Schváleno + Ke kontrole)
        // Manager vidí sumu všech, Zaměstnanec jen svou
        $pendingQuery = WorkShift::query()
            ->whereIn('status', [WorkShift::STATUS_APPROVED, WorkShift::STATUS_PENDING]);

        if (!$user->isManager()) {
            $pendingQuery->where('user_id', $user->id);
        }

        $pendingAmount = $pendingQuery->sum('calculated_wage');


        // 2. Odpracované hodiny tento měsíc
        $hoursQuery = WorkShift::query()
            ->whereMonth('start_at', now()->month)
            ->whereYear('start_at', now()->year)
            ->where('status', '!=', WorkShift::STATUS_REJECTED);

        if (!$user->isManager()) {
            $hoursQuery->where('user_id', $user->id);
        }

        $hours = $hoursQuery->sum('total_hours');


        // 3. Hodinová sazba (jen pro zaměstnance)
        $rateStats = [];
        if (!$user->isManager()) {
            $rateStats[] = Stat::make('Moje sazba', $user->hourly_rate . ' Kč/h')
                ->description('Typ: ' . ($user->salary_type === 'fixed' ? 'Fix' : 'Hodinová'))
                ->color('gray');
        }

        return [
            Stat::make('K vyplacení', number_format($pendingAmount, 0, ',', ' ') . ' Kč')
                ->description('Schválené směny')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('success'),

            Stat::make('Odpracováno (Tento měsíc)', $hours . ' h')
                ->descriptionIcon('heroicon-m-clock')
                ->color('info'),

            ...$rateStats,
        ];
    }
}
