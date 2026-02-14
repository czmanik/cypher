<?php

namespace App\Filament\Widgets;

use App\Models\ChecklistTemplate;
use App\Models\PlannedShift;
use App\Models\ShiftChecklistResult;
use App\Models\User;
use App\Models\WorkShift;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Widgets\Widget;
use Illuminate\Support\Carbon;

class CurrentShiftWidget extends Widget implements HasActions, HasForms
{
    use InteractsWithActions;
    use InteractsWithForms;

    protected static string $view = 'filament.widgets.current-shift-widget';
    protected int | string | array $columnSpan = 'full';
    protected static ?int $sort = -1; // Úplně nahoře

    public ?WorkShift $activeShift = null;

    public function mount(): void
    {
        $this->loadActiveShift();
    }

    public function loadActiveShift(): void
    {
        $this->activeShift = WorkShift::query()
            ->where('user_id', auth()->id())
            ->whereNull('end_at')
            ->first();
    }

    public function startShift(): Action
    {
        return Action::make('startShift')
            ->label('Začít směnu')
            ->icon('heroicon-o-play')
            ->color('success')
            ->requiresConfirmation()
            ->modalHeading('Začít novou směnu')
            ->modalDescription('Opravdu chcete začít směnu právě teď?')
            ->action(function () {
                $user = auth()->user();
                $now = now();

                // 1. Zkusíme najít naplánovanou směnu (dnes +/- 2 hodiny od startu, nebo prostě dnes)
                // Zjednodušení: Hledáme plánovanou směnu, která má začít dnes a není "využitá" (neexistuje k ní workshift? To je složitější check, ale pro teď stačí najít první volnou)
                // Ale WorkShift nemá vazbu na PlannedShift přímo IDčkem (v DB schématu nebylo planned_shift_id).
                // Takže prostě vytvoříme WorkShift.

                // Pokusíme se odhadnout konec podle plánu
                $planned = PlannedShift::where('user_id', $user->id)
                    ->whereDate('start_at', $now->toDateString())
                    ->first();

                WorkShift::create([
                    'user_id' => $user->id,
                    'start_at' => $now,
                    'status' => 'active',
                    'planned_shift_id' => $planned?->id,
                ]);

                Notification::make()
                    ->title('Směna byla zahájena')
                    ->success()
                    ->send();

                $this->redirect(request()->header('Referer'));
            });
    }

    public function endShift(): Action
    {
        return Action::make('endShift')
            ->label('Ukončit směnu')
            ->icon('heroicon-o-stop')
            ->color('danger')
            ->modalHeading('Kontrolní seznam (Checklist)')
            ->modalDescription('Před ukončením směny prosím zkontrolujte následující body.')
            ->modalSubmitActionLabel('Potvrdit a ukončit')
            ->form(function () {
                $user = auth()->user();

                // Načteme šablony pro tohoto uživatele
                // 1. Cílení = all
                // 2. Cílení = type AND user type matches
                // 3. Cílení = user AND user id matches
                $templates = ChecklistTemplate::query()
                    ->where('is_active', true)
                    ->where(function ($query) use ($user) {
                        $query->where('target_type', 'all')
                              ->orWhere(function ($q) use ($user) {
                                  $q->where('target_type', 'type')
                                    ->where('target_employee_type', $user->employee_type);
                              })
                              ->orWhere(function ($q) use ($user) {
                                  $q->where('target_type', 'user')
                                    ->where('target_user_id', $user->id);
                              });
                    })
                    ->orderBy('sort_order')
                    ->get();

                $schema = [];

                if ($templates->isEmpty()) {
                    $schema[] = Forms\Components\Placeholder::make('no_checklist')
                        ->content('Pro vaši pozici nejsou definovány žádné úkoly.');
                }

                foreach ($templates as $template) {
                    $schema[] = Forms\Components\Section::make($template->task_name)
                        ->schema([
                            Forms\Components\Hidden::make("tasks.{$template->id}.template_id")
                                ->default($template->id),

                            Forms\Components\Hidden::make("tasks.{$template->id}.task_name")
                                ->default($template->task_name),

                            Forms\Components\Checkbox::make("tasks.{$template->id}.is_completed")
                                ->label('Splněno')
                                ->required($template->is_required) // Validace povinnosti
                                ->default(false),

                            Forms\Components\TextInput::make("tasks.{$template->id}.note")
                                ->label('Poznámka')
                                ->placeholder('Např. chybělo mýdlo...'),
                        ])
                        ->compact()
                        ->collapsible();
                }

                $schema[] = Forms\Components\Textarea::make('general_note')
                    ->label('Poznámka ke směně')
                    ->placeholder('Něco se stalo? Chybí zboží? Napište to sem...')
                    ->rows(3)
                    ->maxLength(1000);

                $schema[] = Forms\Components\Checkbox::make('should_logout')
                    ->label('Odhlásit se po ukončení směny')
                    ->default(false);

                return $schema;
            })
            ->action(function (array $data) {
                if (!$this->activeShift) return;

                $tasks = $data['tasks'] ?? [];
                $generalNote = $data['general_note'] ?? null;

                // Uložit výsledky
                foreach ($tasks as $taskId => $taskData) {
                    // Ignorujeme placeholder tasks pokud nějaké jsou
                    if (!isset($taskData['template_id'])) continue;

                    ShiftChecklistResult::create([
                        'work_shift_id' => $this->activeShift->id,
                        'task_name' => $taskData['task_name'],
                        'is_completed' => $taskData['is_completed'] ?? false,
                        'note' => $taskData['note'] ?? null,
                    ]);
                }

                // Ukončit směnu
                $this->activeShift->update([
                    'end_at' => now(),
                    'status' => 'pending_approval', // Po ukončení jde ke kontrole
                    'general_note' => $generalNote,
                ]);

                // Přepočet (trigger v modelu se postará, ale end_at je klíčové)
                // Trigger `static::saving` v modelu WorkShift volá calculateStats()
                // Při update se zavolá.

                Notification::make()
                    ->title('Směna byla ukončena')
                    ->success()
                    ->send();

                if (!empty($data['should_logout'])) {
                    auth()->logout();
                    session()->invalidate();
                    session()->regenerateToken();

                    $this->redirect('/admin/login');
                    return;
                }

                $this->redirect(request()->header('Referer'));
            });
    }
}
