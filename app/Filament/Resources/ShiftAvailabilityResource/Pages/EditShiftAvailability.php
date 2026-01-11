<?php

namespace App\Filament\Resources\ShiftAvailabilityResource\Pages;

use App\Filament\Resources\ShiftAvailabilityResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditShiftAvailability extends EditRecord
{
    protected static string $resource = ShiftAvailabilityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
