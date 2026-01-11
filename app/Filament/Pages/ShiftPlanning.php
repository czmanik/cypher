<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Filament\Widgets\ShiftCalendarWidget;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ShiftPlanning extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $navigationGroup = 'HR & Provoz';
    protected static ?string $navigationLabel = 'Plánování Směn';
    protected static ?string $title = 'Kalendář Směn';
    protected static ?int $navigationSort = 2; // Right after Employees/Attendance?

    protected static string $view = 'filament.pages.shift-planning';

    // Only managers can access
    public static function canAccess(): bool
    {
        return Auth::check() && Auth::user()->is_manager;
    }

    protected function getHeaderWidgets(): array
    {
        return [
            ShiftCalendarWidget::class,
        ];
    }
}
