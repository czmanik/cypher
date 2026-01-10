<?php

namespace App\Filament\Widgets;

use App\Models\EventClaim;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class EventClaimsChart extends ChartWidget
{
    protected static ?string $heading = 'Nové registrace voucherů (posledních 30 dní)';
    protected static ?int $sort = 1;

    protected function getData(): array
    {
        // Používáme knihovnu Flowframe Trend (je v Laravelu/Filamentu standard)
        $data = Trend::model(EventClaim::class)
            ->between(
                start: now()->subDays(30),
                end: now(),
            )
            ->perDay()
            ->count();

        return [
            'datasets' => [
                [
                    'label' => 'Počet registrací',
                    'data' => $data->map(fn (TrendValue $value) => $value->aggregate),
                    'backgroundColor' => '#F59E0B', // Cypher Gold barva
                    'borderColor' => '#F59E0B',
                ],
            ],
            'labels' => $data->map(fn (TrendValue $value) => $value->date),
        ];
    }

    protected function getType(): string
    {
        return 'line'; // Nebo 'bar'
    }
}