<?php

namespace App\Filament\Widgets;

use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;
use App\Models\PlannedShift;
use App\Models\User;
use Filament\Forms;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ShiftCalendarWidget extends FullCalendarWidget
{
    // Ikona a pořadí v menu (pokud bys to chtěl jako stránku, ale teď to bude widget na dashboardu)
    protected static ?int $sort = 5;
    protected int | string | array $columnSpan = 'full';
    protected static ?string $heading = 'Plánovač Směn';

    /**
     * Zde načítáme události (směny) z databáze do kalendáře
     */
    public function fetchEvents(array $fetchInfo): array
    {
        return PlannedShift::query()
            ->where('start_at', '>=', $fetchInfo['start'])
            ->where('end_at', '<=', $fetchInfo['end'])
            ->with('user')
            ->get()
            ->map(function (PlannedShift $shift) {
                return [
                    'id'    => $shift->id,
                    'title' => $shift->user->name . ' (' . ($shift->shift_role ?? $shift->user->employee_type) . ')',
                    'start' => $shift->start_at,
                    'end'   => $shift->end_at,
                    'color' => $shift->is_published ? ($shift->color ?? '#3788d8') : '#9ca3af', // Šedá pro koncepty
                    // Další data pro editaci
                    'extendedProps' => [
                        'user_id' => $shift->user_id,
                        'description' => $shift->note,
                    ],
                ];
            })
            ->toArray();
    }

    /**
     * Formulář, který vyskočí, když klikneš do kalendáře
     */
    public function getFormSchema(): array
    {
        return [
            Forms\Components\Select::make('user_id')
                ->label('Zaměstnanec')
                ->options(User::where('is_active', true)->pluck('name', 'id'))
                ->required()
                ->searchable(),

            Forms\Components\Grid::make(2)->schema([
                Forms\Components\DateTimePicker::make('start_at')
                    ->label('Začátek')
                    ->required()
                    ->seconds(false)
                    ->minutesStep(15), // Skákat po 15 min
                
                Forms\Components\DateTimePicker::make('end_at')
                    ->label('Konec')
                    ->required()
                    ->seconds(false)
                    ->minutesStep(15),
            ]),

            Forms\Components\Select::make('shift_role')
                ->label('Pozice pro tuto směnu')
                ->options([
                    'manager' => 'Management',
                    'kitchen' => 'Kuchyň',
                    'floor' => 'Plac / Bar',
                    'support' => 'Pomocný',
                ])
                ->helperText('Nechte prázdné, pokud platí výchozí pozice zaměstnance.'),

            Forms\Components\Textarea::make('note')
                ->label('Poznámka'),

            Forms\Components\Toggle::make('is_published')
                ->label('Zveřejnit směnu')
                ->default(true)
                ->onColor('success')
                ->offColor('gray'),
        ];
    }

    // Co se stane při odeslání formuláře (Vytvoření / Editace)
    // Plugin to řeší automaticky, pokud definujeme model!
    // Ale musíme mu říct, jak mapovat data.
    
    protected function headerActions(): array
    {
        return [
            // Tlačítko pro vytvoření směny (kdyby někdo nechtěl klikat do kalendáře)
            // FullCalendar to má vestavěné v klikání do dnů
        ];
    }
    
    // Povolení editace po kliknutí na událost
    protected function modalActions(): array
    {
        return [
            \Filament\Actions\EditAction::make()
                ->mountUsing(
                    function (PlannedShift $record, Forms\Form $form, array $arguments) {
                        $form->fill([
                            'user_id' => $record->user_id,
                            'start_at' => $record->start_at,
                            'end_at' => $record->end_at,
                            'shift_role' => $record->shift_role,
                            'note' => $record->note,
                            'is_published' => $record->is_published,
                        ]);
                    }
                ),
            \Filament\Actions\DeleteAction::make(),
        ];
    }

    // Důležité: Řekneme widgetu, s jakým modelem pracuje
    public function getModel(): string
    {
        return PlannedShift::class;
    }
}