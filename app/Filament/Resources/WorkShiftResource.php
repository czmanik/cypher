<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WorkShiftResource\Pages;
use App\Models\WorkShift;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Columns\Summarizers\Sum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class WorkShiftResource extends Resource
{
    protected static ?string $model = WorkShift::class;
    protected static ?string $navigationIcon = 'heroicon-o-clock';
    protected static ?string $navigationLabel = 'Docházka & Výplaty';
    protected static ?string $navigationGroup = 'HR & Provoz';

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        if (! auth()->user()?->is_manager) {
            $query->where('user_id', auth()->id());
        }

        return $query;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Detail Směny')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->relationship('user', 'name')
                            ->label('Zaměstnanec')
                            ->required()
                            ->searchable()
                            ->columnSpan(2)
                            // Zaměstnanec nemůže měnit uživatele (pokud by se dostal k vytvoření)
                            ->disabled(fn () => ! auth()->user()?->is_manager),

                        Forms\Components\DateTimePicker::make('start_at')
                            ->label('Začátek')
                            ->required()
                            ->disabled(fn () => ! auth()->user()?->is_manager),

                        Forms\Components\DateTimePicker::make('end_at')
                            ->label('Konec')
                            ->helperText('Pokud nevyplníte, směna stále běží.')
                            ->disabled(fn () => ! auth()->user()?->is_manager),

                        Forms\Components\Select::make('status')
                            ->options([
                                'active' => 'Právě běží',
                                'pending_approval' => 'Ke schválení',
                                'approved' => 'Schváleno (Čeká na platbu)',
                                'paid' => 'Proplaceno (Hotovo)',
                                'rejected' => 'Zamítnuto / Chyba',
                            ])
                            ->required()
                            ->default('active')
                            ->disabled(fn () => ! auth()->user()?->is_manager),
                    ])->columns(2),

                Forms\Components\Section::make('Reporty a Poznámky')
                    ->schema([
                        Forms\Components\Textarea::make('general_note')
                            ->label('Poznámka od zaměstnance')
                            ->disabled(fn () => ! auth()->user()?->is_manager), // Povolíme jen přes widget? Nebo necháme editovat? Nechme jen číst, editace přes widget/tlačítka.
                        
                        Forms\Components\Textarea::make('manager_note')
                            ->label('Interní poznámka manažera')
                            ->disabled(fn () => ! auth()->user()?->is_manager),
                    ])->columns(2),
                
                Forms\Components\Section::make('Finance (Automatický výpočet)')
                    ->schema([
                        Forms\Components\TextInput::make('total_hours')
                            ->label('Celkem hodin')
                            ->suffix('h')
                            ->disabled(),
                        
                        Forms\Components\TextInput::make('calculated_wage')
                            ->label('K výplatě')
                            ->suffix('Kč')
                            ->disabled(),
                    ])->columns(2)
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Zaměstnanec')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('start_at')
                    ->label('Datum')
                    ->dateTime('d.m.Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('time_range')
                    ->label('Čas')
                    ->state(fn (WorkShift $record) => 
                        $record->start_at->format('H:i') . ' - ' . ($record->end_at ? $record->end_at->format('H:i') : '???')
                    ),

                Tables\Columns\TextColumn::make('total_hours')
                    ->label('Hodiny')
                    ->alignRight()
                    ->summarize(Sum::make()->label('Celkem h')),

                Tables\Columns\TextColumn::make('calculated_wage')
                    ->label('Mzda')
                    ->money('CZK')
                    ->alignRight()
                    ->sortable()
                    ->summarize(Sum::make()->money('CZK')->label('Celkem')),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'info',
                        'pending_approval' => 'warning',
                        'approved' => 'primary',
                        'paid' => 'success',
                        'rejected' => 'danger',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'active' => 'Běží',
                        'pending_approval' => 'Ke kontrole',
                        'approved' => 'K úhradě',
                        'paid' => 'Proplaceno',
                        'rejected' => 'Zamítnuto',
                        default => $state,
                    }),
            ])
            ->defaultSort('start_at', 'desc')
            ->filters([
                SelectFilter::make('user_id')
                    ->label('Zaměstnanec')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->visible(fn () => auth()->user()?->is_manager), // Filtr zaměstnance jen pro manažera

                SelectFilter::make('status')
                    ->options([
                        'pending_approval' => 'Ke kontrole',
                        'approved' => 'K úhradě',
                        'paid' => 'Proplaceno',
                    ]),

                Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')->label('Od data'),
                        Forms\Components\DatePicker::make('created_until')->label('Do data'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date) => $query->whereDate('start_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date) => $query->whereDate('start_at', '<=', $date),
                            );
                    })
            ])
            ->actions([
                Tables\Actions\Action::make('view_checklist')
                    ->label('Checklist')
                    ->icon('heroicon-o-clipboard-document-check')
                    ->color('info')
                    ->modalHeading('Výsledky Checklistu')
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Zavřít')
                    ->modalContent(fn (WorkShift $record) => view('filament.resources.work-shift-resource.pages.checklist-modal', ['record' => $record]))
                    ->visible(fn () => auth()->user()?->is_manager),

                Tables\Actions\EditAction::make()
                    ->visible(fn () => auth()->user()?->is_manager),
                
                Tables\Actions\Action::make('approve')
                    ->label('Schválit')
                    ->icon('heroicon-o-check')
                    ->color('primary')
                    ->visible(fn (WorkShift $record) => $record->status === 'pending_approval' && auth()->user()?->is_manager)
                    ->action(function (WorkShift $record) {
                        $record->update(['status' => 'approved']);
                    }),

                Tables\Actions\Action::make('mark_paid')
                    ->label('Proplatit')
                    ->icon('heroicon-o-currency-dollar')
                    ->color('success')
                    ->visible(fn (WorkShift $record) => $record->status === 'approved' && auth()->user()?->is_manager)
                    ->requiresConfirmation()
                    ->modalHeading('Potvrdit vyplacení')
                    ->modalDescription(fn (WorkShift $record) => 'Opravdu označit směnu zaměstnance ' . $record->user->name . ' za proplacenou? Částka: ' . $record->calculated_wage . ' Kč')
                    ->modalSubmitActionLabel('Ano, proplaceno')
                    ->action(function (WorkShift $record) {
                        $record->update(['status' => 'paid']);
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()?->is_manager),
                    
                    Tables\Actions\BulkAction::make('approve_all')
                        ->label('Schválit označené')
                        ->icon('heroicon-o-check')
                        ->color('primary')
                        ->visible(fn () => auth()->user()?->is_manager)
                        ->action(fn (Collection $records) => $records->each->update(['status' => 'approved'])),

                    Tables\Actions\BulkAction::make('pay_all')
                        ->label('Proplatit označené')
                        ->icon('heroicon-o-currency-dollar')
                        ->color('success')
                        ->requiresConfirmation()
                        ->visible(fn () => auth()->user()?->is_manager)
                        ->action(fn (Collection $records) => $records->each->update(['status' => 'paid'])),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWorkShifts::route('/'),
            'create' => Pages\CreateWorkShift::route('/create'),
            'edit' => Pages\EditWorkShift::route('/{record}/edit'),
        ];
    }
}
