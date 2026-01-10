<?php

namespace App\Filament\Resources\EventClaimResource\Pages;

use App\Filament\Resources\EventClaimResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEventClaim extends EditRecord
{
    protected static string $resource = EventClaimResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
