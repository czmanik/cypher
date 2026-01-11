<?php

namespace App\Filament\Actions;

use Filament\Actions\Action;
use App\Models\PlannedShift;
use App\Models\ShiftAuditLog;
use Filament\Forms\Components\Placeholder;
use Filament\Support\Enums\MaxWidth;

class ViewShiftHistoryAction extends Action
{
    public static function make(string $name = 'viewHistory'): static
    {
        return parent::make($name)
            ->label('Historie')
            ->icon('heroicon-o-clock')
            ->modalHeading('Historie směny')
            ->modalWidth(MaxWidth::Medium)
            ->modalContent(fn (PlannedShift $record) => view('filament.actions.shift-history', ['logs' => $record->auditLogs()->with('user')->latest()->get()]))
            ->modalSubmitAction(false)
            ->modalCancelAction(fn (Action $action) => $action->label('Zavřít'));
    }
}
