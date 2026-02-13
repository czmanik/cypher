<?php

namespace App\Filament\Pages;

use App\Models\WorkShift;
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
    public $totalGuests = 0;
    public $totalTips = 0.0;
    public $totalDiscount = 0.0;

    public $operationalFrom = null;
    public $operationalTill = null;

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

    public function previousDay()
    {
        $newDate = Carbon::parse($this->date)->subDay();
        $this->date = $newDate->format('Y-m-d');
        $this->loadDataForDate($newDate);
    }

    public function nextDay()
    {
        $newDate = Carbon::parse($this->date)->addDay();

        if ($newDate->isFuture()) {
            return;
        }

        $this->date = $newDate->format('Y-m-d');
        $this->loadDataForDate($newDate);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('previousDay')
                ->label('Předchozí den')
                ->icon('heroicon-o-chevron-left')
                ->color('gray')
                ->action(fn () => $this->previousDay()),

            Action::make('nextDay')
                ->label('Následující den')
                ->icon('heroicon-o-chevron-right')
                ->color('gray')
                ->action(fn () => $this->nextDay()),

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
        // 1. Zkusíme najít základní data v seznamu (pro rychlé zobrazení, než se načte detail,
        // i když v tomto synchronním flow to moc nepomůže, ale jako fallback dobrý)
        $basicInfo = collect($this->bills)->firstWhere('billId', $billId);

        // 2. Načteme plný detail (položky) z API
        /** @var StoryousService $service */
        $service = app(StoryousService::class);
        $detail = $service->getBillDetail($billId);

        // Pokud API vrátí detail, použijeme ho. Jinak fallback na základní info.
        $this->selectedBill = $detail ?? $basicInfo;

        $this->dispatch('open-modal', id: 'bill-detail-modal');
    }

    public function loadDataForDate(Carbon $date)
    {
        /** @var StoryousService $service */
        $service = app(StoryousService::class);

        // Calculate operational range based on shifts
        $range = $this->calculateOperationalTimes($date);
        $this->operationalFrom = $range['from'];
        $this->operationalTill = $range['till'];

        // Fetch bills with custom range
        $this->bills = $service->getBillsForDate($date, $this->operationalFrom, $this->operationalTill);

        $this->billsCount = count($this->bills);

        $this->totalRevenue = collect($this->bills)->sum(function ($bill) {
            return $bill['finalPrice'] ?? $bill['totalAmount'] ?? 0.0;
        });

        $this->totalGuests = collect($this->bills)->sum(function ($bill) {
            return (int)($bill['personCount'] ?? 0);
        });

        $this->totalTips = collect($this->bills)->sum(function ($bill) {
            return (float)($bill['tips'] ?? 0.0);
        });

        $this->totalDiscount = collect($this->bills)->sum(function ($bill) {
            return (float)($bill['discount'] ?? 0.0);
        });
    }

    protected function calculateOperationalTimes(Carbon $date): array
    {
        // Najít směny začínající v daný den (00:00 - 23:59)
        $shifts = WorkShift::whereDate('start_at', $date)->get();

        if ($shifts->isEmpty()) {
            // Pokud nejsou směny, bereme celý den 00:00 - 23:59 (default)
            return [
                'from' => $date->copy()->startOfDay(),
                'till' => $date->copy()->endOfDay(),
            ];
        }

        // Začátek = start první směny
        $minStart = $shifts->min('start_at');

        // Konec = konec poslední směny. Pokud je nějaká směna aktivní (end_at=null), tak "teď".
        $activeShifts = $shifts->whereNull('end_at');
        if ($activeShifts->isNotEmpty()) {
            $maxEnd = now();
            // Pokud se díváme do historie a někdo neuzavřel směnu, mohlo by to vzít "teď" i po měsíci.
            // Ošetření: Pokud den není dnešek, omezíme maxEnd na konec dne + 6h (např. 06:00 ráno další den)
            // Ale pro jednoduchost, pokud je to historie, bereme konec dne, pokud směna nebyla ukončena.
            if (!$date->isToday()) {
                $maxEnd = $date->copy()->endOfDay(); // Fallback
            }
        } else {
            $maxEnd = $shifts->max('end_at');
        }

        return [
            'from' => $minStart ? Carbon::parse($minStart) : $date->copy()->startOfDay(),
            'till' => $maxEnd ? Carbon::parse($maxEnd) : $date->copy()->endOfDay(),
        ];
    }
}
