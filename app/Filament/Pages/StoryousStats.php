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
    protected static ?string $navigationGroup = null; // Zrušíme skupinu, aby byla stránka nahoře
    protected static ?string $navigationLabel = 'Storyous Statistiky'; // Změníme název pro jistotu
    protected static ?string $title = 'Storyous Přehled';
    protected static ?int $navigationSort = 1; // První místo

    protected static string $view = 'filament.pages.storyous-stats';

    public $date;
    public $totalRevenue = 0.0;
    public $billsCount = 0;
    public $bills = [];
    public $selectedBill = null;

    public function mount()
    {
        // Check if date is passed in query string (e.g. from redirect)
        $this->date = request()->query('date', now()->format('Y-m-d'));
        $this->loadDataForDate(Carbon::parse($this->date));
    }

    public static function canAccess(): bool
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        // Pokud je uživatel přihlášen, zkusíme ho pustit, pokud je manažer nebo admin.
        // Pro jistotu explicitně přetypujeme na bool.
        if (!$user) {
            return false;
        }

        return (bool) ($user->is_manager || $user->is_admin);
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
                    $this->loadDataForDate($newDate);
                }),

            Action::make('nextDay')
                ->label('Následující den')
                ->icon('heroicon-o-chevron-right')
                ->color('gray')
                ->action(function () {
                    $newDate = Carbon::parse($this->date)->addDay();
                    $this->date = $newDate->format('Y-m-d');
                    $this->loadDataForDate($newDate);
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

            Action::make('refresh')
                ->label('Aktualizovat')
                ->icon('heroicon-o-arrow-path')
                ->color('primary')
                ->action(function () {
                    // Force refresh cache for the selected date
                    $cacheKey = 'storyous_bills_' . Carbon::parse($this->date)->format('Y-m-d');
                    Cache::forget($cacheKey);

                    $this->loadDataForDate(Carbon::parse($this->date));
                }),
        ];
    }

    public function openBillDetail(string $billId)
    {
        $this->selectedBill = collect($this->bills)->firstWhere('billId', $billId);
        $this->dispatch('open-modal', id: 'bill-detail-modal');
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
