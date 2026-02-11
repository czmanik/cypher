<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Services\StoryousService;

class StoryousSalesStats extends BaseWidget
{
    protected static ?int $sort = 0; // Top position

    public static function canView(): bool
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        // Visible to Managers and Admins
        return $user && ($user->is_manager || $user->is_admin);
    }

    protected function getStats(): array
    {
        /** @var StoryousService $service */
        $service = app(StoryousService::class);

        $todayRevenue = $service->getRevenueForDate(now());

        return [
            Stat::make('Dnešní tržby (Storyous)', number_format($todayRevenue, 0, ',', ' ') . ' Kč')
                ->description('Data ze Storyous API')
                ->descriptionIcon('heroicon-m-banknotes')
                ->chart([7, 2, 10, 3, 15, 4, 17]) // Mock chart for visual appeal
                ->color('success'),
        ];
    }
}
