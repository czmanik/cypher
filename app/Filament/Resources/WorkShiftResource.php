<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WorkShiftResource\Pages;
use App\Filament\Resources\WorkShiftResource\RelationManagers;
use App\Models\WorkShift;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\Summarizers\Summarizer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

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

                Tables\Columns\TextColumn::make('bonus_malus')
                    ->label('Bonus / Malus')
                    ->state(fn (WorkShift $record) => $record->bonus - $record->penalty)
                    ->money('CZK')
                    ->alignRight()
                    ->color(fn (string $state) => $state < 0 ? 'danger' : ($state > 0 ? 'success' : null))
                    ->summarize(Summarizer::make()
                        ->money('CZK')
                        ->label('Celkem')
                        ->using(fn ($query) => $query->sum(DB::raw('bonus - penalty')))
                    ),

                Tables\Columns\TextColumn::make('final_payout')
                    ->label('K výplatě')
                    ->money('CZK')
                    ->alignRight()
                    ->weight('bold')
                    ->summarize(Summarizer::make()
                        ->money('CZK')
                        ->label('Celkem')
                        ->using(fn ($query) => $query->sum(DB::raw('calculated_wage + bonus - penalty - advance_amount')))
                    ),

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
                Tables\Actions\Action::make('view_detail')
                    ->label('Detail')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->modalHeading('Detail směny')
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Zavřít')
                    ->modalContent(fn (WorkShift $record) => view('filament.resources.work-shift-resource.pages.shift-detail-modal', ['record' => $record])),

                Tables\Actions\EditAction::make()
                    ->visible(fn () => auth()->user()?->is_manager),
                
                Tables\Actions\Action::make('approve')
                    ->label('Schválit')
                    ->icon('heroicon-o-check')
                    ->color('primary')
                    ->visible(fn (WorkShift $record) => $record->status === 'pending_approval' && auth()->user()?->is_manager)
                    ->form([
                        Forms\Components\TextInput::make('advance_amount')
                            ->label('Záloha (již vyplaceno)')
                            ->numeric()
                            ->prefix('Kč')
                            ->default(0),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('bonus')
                                    ->label('Bonus')
                                    ->numeric()
                                    ->prefix('Kč')
                                    ->default(0)
                                    ->live(),

                                Forms\Components\Textarea::make('bonus_note')
                                    ->label('Důvod bonusu')
                                    ->rows(2)
                                    ->visible(fn (Forms\Get $get) => (float)$get('bonus') > 0),
                            ]),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('penalty')
                                    ->label('Pokuta (Malus)')
                                    ->numeric()
                                    ->prefix('Kč')
                                    ->default(0)
                                    ->live(),

                                Forms\Components\Textarea::make('penalty_note')
                                    ->label('Důvod pokuty')
                                    ->rows(2)
                                    ->required(fn (Forms\Get $get) => (float)$get('penalty') > 0)
                                    ->visible(fn (Forms\Get $get) => (float)$get('penalty') > 0),
                            ]),
                    ])
                    ->action(function (WorkShift $record, array $data) {
                        $record->update([
                            'status' => 'approved',
                            'advance_amount' => $data['advance_amount'],
                            'bonus' => $data['bonus'],
                            'bonus_note' => $data['bonus_note'] ?? null,
                            'penalty' => $data['penalty'],
                            'penalty_note' => $data['penalty_note'] ?? null,
                        ]);
                    }),

                Tables\Actions\Action::make('mark_paid')
                    ->label('Proplatit')
                    ->icon('heroicon-o-currency-dollar')
                    ->color('success')
                    ->visible(fn (WorkShift $record) => $record->status === 'approved' && auth()->user()?->is_manager)
                    ->modalHeading('Vyplacení mzdy')
                    ->form(fn (WorkShift $record) => [
                        Forms\Components\Placeholder::make('summary')
                            ->label('Rekapitulace')
                            ->content(new \Illuminate\Support\HtmlString(
                                '<strong>Základní mzda:</strong> ' . number_format($record->calculated_wage, 2, ',', ' ') . ' Kč<br>' .
                                '<strong>Bonus:</strong> +' . number_format($record->bonus, 2, ',', ' ') . ' Kč<br>' .
                                '<strong>Pokuta:</strong> -' . number_format($record->penalty, 2, ',', ' ') . ' Kč<br>' .
                                '<strong>Záloha:</strong> -' . number_format($record->advance_amount, 2, ',', ' ') . ' Kč<br>' .
                                '<hr>' .
                                '<strong class="text-xl text-primary-600">K úhradě: ' . number_format($record->final_payout, 2, ',', ' ') . ' Kč</strong>'
                            )),

                        Forms\Components\Select::make('payment_method')
                            ->label('Způsob platby')
                            ->options([
                                'cash' => 'Hotově',
                                'bank_transfer' => 'Na účet',
                            ])
                            ->required()
                            ->default('cash'),
                    ])
                    ->modalSubmitActionLabel('Potvrdit a Proplatit')
                    ->action(function (WorkShift $record, array $data) {
                        $record->update([
                            'status' => 'paid',
                            'payment_method' => $data['payment_method'],
                        ]);
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
                        ->visible(fn () => auth()->user()?->is_manager)
                        ->form([
                            Forms\Components\Select::make('payment_method')
                                ->label('Způsob platby')
                                ->options([
                                    'cash' => 'Hotově',
                                    'bank_transfer' => 'Na účet',
                                ])
                                ->required()
                                ->default('cash'),
                        ])
                        ->action(fn (Collection $records, array $data) => $records->each->update([
                            'status' => 'paid',
                            'payment_method' => $data['payment_method'],
                        ])),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ActivitiesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWorkShifts::route('/'),
            'create' => Pages\CreateWorkShift::route('/create'),
            'view' => Pages\ViewWorkShift::route('/{record}'),
            'edit' => Pages\EditWorkShift::route('/{record}/edit'),
        ];
    }
}
