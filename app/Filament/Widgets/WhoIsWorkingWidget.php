<?php

namespace App\Filament\Widgets;

use App\Models\PlannedShift;
use Filament\Widgets\Widget;
use Carbon\Carbon;

class WhoIsWorkingWidget extends Widget
{
    protected static string $view = 'filament.widgets.who-is-working-widget';
    protected static ?int $sort = 1;
    protected int | string | array $columnSpan = 'full';

    public $activeShifts = [];
    public $upcomingShifts = [];
    public $tomorrowShifts = [];

    public function mount()
    {
        $now = now();
        $todayEnd = now()->endOfDay();
        $tomorrowStart = now()->addDay()->startOfDay();
        $tomorrowEnd = now()->addDay()->endOfDay();

        // 1. Právě teď na směně
        $this->activeShifts = PlannedShift::with('user')
            ->where('start_at', '<=', $now)
            ->where('end_at', '>=', $now)
            ->whereNotNull('user_id')
            ->orderBy('end_at')
            ->get();

        // 2. Dnes později
        $this->upcomingShifts = PlannedShift::with('user')
            ->where('start_at', '>', $now)
            ->where('start_at', '<=', $todayEnd)
            ->whereNotNull('user_id')
            ->orderBy('start_at')
            ->get();

        // 3. Zítra
        $this->tomorrowShifts = PlannedShift::with('user')
            ->where('start_at', '>=', $tomorrowStart)
            ->where('start_at', '<=', $tomorrowEnd)
            ->whereNotNull('user_id')
            ->orderBy('start_at')
            ->get();
    }
}
