<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Services\StoryousService;
use Carbon\Carbon;

class StoryousSalesStats extends BaseWidget
{
    protected static ?int $sort = 0; // Top position

    // Auto-poll every 60 seconds (optional, keeps data somewhat fresh)
    protected static ?string $pollingInterval = '60s';

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

        // We use 'now()' which includes time, but the service handles day-level caching
        $todayRevenue = $service->getRevenueForDate(now());
        $todayTips = $service->getTipsForDate(now());

        return [
            Stat::make('Dnešní tržby (Storyous)', number_format($todayRevenue, 0, ',', ' ') . ' Kč')
                ->description('Aktuální data ze Storyous API')
                ->descriptionIcon('heroicon-m-banknotes')
                ->chart([7, 2, 10, 3, 15, 4, 17]) // Mock chart for visual appeal
                ->color('success')
                // Add an extra attribute to indicate freshness (optional)
                ->extraAttributes([
                    'class' => 'cursor-pointer',
                    'title' => 'Kliknutím aktualizujete data',
                    'wire:click' => '$refresh', // Simple Livewire refresh
                ]),

            Stat::make('Dnešní spropitné (Storyous)', number_format($todayTips, 0, ',', ' ') . ' Kč')
                ->description('Spropitné ze Storyous API')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('warning')
                ->extraAttributes([
                    'class' => 'cursor-pointer',
                    'wire:click' => '$refresh',
                ]),
        ];
    }
}
