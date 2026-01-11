<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\ShiftCalendarWidget;
use App\Models\PlannedShift;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;

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
        // Akce pouze pro Managera
        if (!auth()->user()?->isManager()) {
            return [];
        }

        return [
            Action::make('publish_all')
                ->label('Zveřejnit vše (Drafty)')
                ->icon('heroicon-o-megaphone')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Zveřejnit všechny koncepty?')
                ->modalDescription('Všechny směny ve stavu "Draft" budou převedeny na "Nařízeno" (Ordered) a zaměstnanci je uvidí.')
                ->action(function () {
                    $count = PlannedShift::where('status', PlannedShift::STATUS_DRAFT)->update(['status' => PlannedShift::STATUS_ORDERED]);

                    Notification::make()
                        ->title("Zveřejněno $count směn")
                        ->success()
                        ->send();

                    // Refresh widgetu - musíme vyslat event, na který widget naslouchá
                    $this->dispatch('filament-fullcalendar:refresh');
                }),
        ];
    }
}
