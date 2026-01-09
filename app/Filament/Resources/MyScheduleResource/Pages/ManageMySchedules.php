<?php

namespace App\Filament\Resources\MyScheduleResource\Pages;

use App\Filament\Resources\MyScheduleResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageMySchedules extends ManageRecords
{
    protected static string $resource = MyScheduleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
