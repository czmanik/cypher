<?php

namespace App\Filament\Pages;

use App\Services\StoryousService;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Cache;

class StoryousStats extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationGroup = 'HR & Provoz';
    protected static ?string $navigationLabel = 'Storyous';
    protected static ?string $title = 'Storyous Přehled';

    protected static string $view = 'filament.pages.storyous-stats';

    public $date;
    public $totalRevenue = 0.0;
    public $billsCount = 0;
    public $bills = [];

    public function mount()
    {
        $this->date = now()->format('Y-m-d');
        $this->loadDataForDate(now());
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
            Action::make('refresh')
                ->label('Načíst data (Aktualizovat)')
                ->icon('heroicon-o-arrow-path')
                ->color('primary')
                ->action(function () {
                    // Force refresh cache for the selected date
                    $cacheKey = 'storyous_bills_' . Carbon::parse($this->date)->format('Y-m-d');
                    Cache::forget($cacheKey);

                    $this->loadDataForDate(Carbon::parse($this->date));
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
                    $this->loadDataForDate(Carbon::parse($this->date));
                }),
        ];
    }

    public function loadDataForDate(Carbon $date)
    {
        /** @var StoryousService $service */
        $service = app(StoryousService::class);

        // Fetch bills (uses cache unless cleared)
        $this->bills = $service->getBillsForDate($date);

        $this->billsCount = count($this->bills);

        $this->totalRevenue = collect($this->bills)->sum(function ($bill) {
            return $bill['finalPrice'] ?? $bill['totalAmount'] ?? 0.0;
        });
    }
}
