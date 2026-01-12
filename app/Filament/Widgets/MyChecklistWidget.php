<?php

namespace App\Filament\Widgets;

use App\Models\WorkShift;
use App\Models\ChecklistTemplate;
use App\Models\ShiftChecklistResult;
use Filament\Widgets\Widget;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Actions\Action;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;

class MyChecklistWidget extends Widget implements HasForms
{
    use InteractsWithForms;

    protected static string $view = 'filament.widgets.my-checklist-widget';
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = 'full';

    // Data pro view
    public ?WorkShift $activeShift = null;
    public $checklistItems = [];

    public function mount()
    {
        $this->loadData();
    }

    public function loadData()
    {
        $user = auth()->user();
        if (!$user) return;

        // Najdi aktivní směnu
        $this->activeShift = WorkShift::where('user_id', $user->id)
            ->where('status', WorkShift::STATUS_ACTIVE)
            ->first();

        if ($this->activeShift) {
            // 1. Zjistíme, jestli už máme vygenerované výsledky
            $existingResults = $this->activeShift->checklistResults()->get();

            if ($existingResults->isEmpty()) {
                // 2. Pokud ne, vygenerujeme je ze šablon
                // Filtrování šablon podle role/uživatele by bylo super, ale pro teď vezmeme aktivní
                $templates = ChecklistTemplate::where('is_active', true)
                    ->orderBy('sort_order')
                    ->get();

                foreach ($templates as $tpl) {
                    $this->activeShift->checklistResults()->create([
                        'task_name' => $tpl->task_name,
                        'is_completed' => false,
                    ]);
                }

                $this->checklistItems = $this->activeShift->checklistResults()->get();
            } else {
                $this->checklistItems = $existingResults;
            }
        }
    }

    public function toggleItem($itemId)
    {
        $item = ShiftChecklistResult::find($itemId);
        if ($item && $this->activeShift && $item->work_shift_id === $this->activeShift->id) {
            $item->is_completed = !$item->is_completed;
            $item->save();

            Notification::make()->title('Uloženo')->success()->send();

            // Reload
            $this->loadData();
        }
    }

    // Zobrazení jen pokud mám aktivní směnu
    public static function canView(): bool
    {
        $user = auth()->user();
        if (!$user) return false;

        // Optimalizace: Checknout DB count
        return WorkShift::where('user_id', $user->id)
            ->where('status', WorkShift::STATUS_ACTIVE)
            ->exists();
    }
}
