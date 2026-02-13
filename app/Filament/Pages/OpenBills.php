<?php

namespace App\Filament\Pages;

use App\Services\StoryousService;
use Filament\Actions\Action;
use Filament\Pages\Page;
use Illuminate\Support\Collection;

class OpenBills extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-receipt-refund';
    protected static ?string $navigationLabel = 'Otevřené účty';
    protected static ?string $title = 'Otevřené účty';
    protected static ?string $navigationGroup = 'HR & Provoz';
    protected static ?int $navigationSort = 3;
    protected static bool $shouldRegisterNavigation = false; // Hidden from sidebar, accessed via Dashboard

    protected static string $view = 'filament.pages.open-bills';

    public $openBills = [];
    public $totalAmount = 0.0;

    public function mount()
    {
        $this->loadData();
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
                ->label('Aktualizovat')
                ->icon('heroicon-o-arrow-path')
                ->action(fn () => $this->loadData()),
        ];
    }

    public function loadData()
    {
        /** @var StoryousService $service */
        $service = app(StoryousService::class);
        $rawBills = $service->getOpenBills();

        // Filter out "personální" category if possible
        // We assume bill object has 'category' or similar.
        // Since we don't know the exact structure of "Open Bill" (Table object),
        // we will inspect it or filter if 'categoryName' exists.

        $this->openBills = collect($rawBills)->filter(function ($bill) {
            // Placeholder logic: check generic fields for "personální"
            // Adjust based on real API response
            $category = $bill['categoryName'] ?? $bill['category'] ?? '';
            if (stripos($category, 'personální') !== false) {
                return false;
            }
            return true;
        })->values()->toArray();

        $this->totalAmount = collect($this->openBills)->sum(function ($bill) {
             // 'total' or 'price' or 'amount'
             return (float)($bill['totalAmount'] ?? $bill['finalPrice'] ?? $bill['price'] ?? 0);
        });
    }
}
