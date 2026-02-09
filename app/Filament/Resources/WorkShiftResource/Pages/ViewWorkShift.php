<?php

namespace App\Filament\Resources\WorkShiftResource\Pages;

use App\Filament\Resources\WorkShiftResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewWorkShift extends ViewRecord
{
    protected static string $resource = WorkShiftResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions like Approve/Pay could be added here if needed,
            // but for now we keep it simple for employees.
        ];
    }
}
