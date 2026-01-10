<?php

namespace App\Filament\Resources\EventClaimResource\Pages;

use App\Filament\Resources\EventClaimResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEventClaims extends ListRecords
{
    protected static string $resource = EventClaimResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
{
    return [
        \App\Filament\Widgets\EventClaimsChart::class,
    ];
}
}
