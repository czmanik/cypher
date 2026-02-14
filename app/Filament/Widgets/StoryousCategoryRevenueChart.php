<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Carbon\Carbon;
use Livewire\Attributes\On;

class StoryousCategoryRevenueChart extends ChartWidget
{
    protected static ?string $heading = 'Podíl tržeb dle kategorií';
    protected static ?string $maxHeight = '300px';

    public ?string $date = null;

    public function mount(?string $date = null)
    {
        $this->date = $date ?? now()->format('Y-m-d');
    }

    #[On('storyous-date-updated')]
    public function updateDate(string $date): void
    {
        $this->date = $date;
        $this->updateChartData();
    }

    protected function getData(): array
    {
        $date = $this->date ? Carbon::parse($this->date) : now();
        $startOfDay = $date->copy()->startOfDay();
        $endOfDay = $date->copy()->endOfDay();

        $data = \App\Models\BillItem::query()
            ->join('bills', 'bill_items.bill_id', '=', 'bills.id')
            ->leftJoin('products', 'bill_items.product_id', '=', 'products.id')
            ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
            ->whereBetween('bills.paid_at', [$startOfDay, $endOfDay])
            ->selectRaw('
                COALESCE(categories.name, "Nezařazeno") as category_name,
                SUM(bill_items.total_price) as total_revenue
            ')
            ->groupBy('category_name')
            ->orderByDesc('total_revenue')
            ->get();

        $labels = $data->pluck('category_name')->toArray();
        $values = $data->pluck('total_revenue')->toArray();

        // Color palette (simple rotation)
        $colors = [
            '#3b82f6', '#ef4444', '#10b981', '#f59e0b', '#8b5cf6',
            '#ec4899', '#6366f1', '#14b8a6', '#f97316', '#64748b'
        ];

        return [
            'datasets' => [
                [
                    'label' => 'Tržba (Kč)',
                    'data' => $values,
                    'backgroundColor' => array_slice($colors, 0, count($values)),
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
