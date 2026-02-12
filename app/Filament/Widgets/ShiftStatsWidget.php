<?php

namespace App\Filament\Widgets;

use App\Models\WorkShift;
use App\Services\StoryousService;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class ShiftStatsWidget extends BaseWidget
{
    // High polling interval to keep data fresh but not overload API (cached anyway)
    protected static ?string $pollingInterval = '60s';

    // Show below CurrentShiftWidget (usually sort -1) but above tables.
    // Let's set it to 1 to be safe, or check existing sort orders.
    protected static ?int $sort = 1;

    public static function canView(): bool
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$user) {
            return false;
        }

        // Managers and Admins always see it
        if ($user->is_manager || $user->is_admin) {
            return true;
        }

        // Employees see it ONLY if they have an active shift
        // We check for a shift that has started but not ended (end_at is null)
        return WorkShift::query()
            ->where('user_id', $user->id)
            ->whereNull('end_at')
            ->exists();
    }

    protected function getStats(): array
    {
        /** @var StoryousService $service */
        $service = app(StoryousService::class);
        $now = now();

        $tips = $service->getTipsForDate($now);
        $guests = $service->getPersonCountForDate($now);

        return [
            Stat::make('Dnešní spropitné', number_format($tips, 0, ',', ' ') . ' Kč')
                ->description('Celkem z účtenek')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'), // Green implies money/good

            Stat::make('Počet hostů', number_format($guests, 0, ',', ' '))
                ->description('Dnes obslouženo')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'), // Blue implies neutral info
        ];
    }
}
