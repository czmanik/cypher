<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WorkShiftResource\Pages;
use App\Models\WorkShift;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class WorkShiftResource extends Resource
{
    protected static ?string $model = WorkShift::class;
    protected static ?string $navigationIcon = 'heroicon-o-clock';
    protected static ?string $navigationLabel = 'Docházka & Směny';
    protected static ?string $navigationGroup = 'HR & Provoz';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->label('Zaměstnanec')
                    ->required(),

                Forms\Components\Grid::make(2)->schema([
                    Forms\Components\DateTimePicker::make('start_at')
                        ->label('Začátek')
                        ->required(),
                    Forms\Components\DateTimePicker::make('end_at')
                        ->label('Konec'),
                ]),

                Forms\Components\Select::make('status')
                    ->options([
                        'active' => 'Právě běží',
                        'pending_approval' => 'Ke schválení',
                        'approved' => 'Schváleno',
                        'rejected' => 'Zamítnuto',
                    ])
                    ->required(),
                
                Forms\Components\Textarea::make('general_note')
                    ->label('Report zaměstnance')
                    ->columnSpanFull(),
                
                Forms\Components\Textarea::make('manager_note')
                    ->label('Poznámka manažera')
                    ->columnSpanFull(),
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
                    ->label('Start')
                    ->dateTime('d.m. H:i')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('end_at')
                    ->label('Konec')
                    ->dateTime('d.m. H:i')
                    ->placeholder('Běží...'),

                Tables\Columns\TextColumn::make('total_hours') // To budeme počítat automaticky
                    ->label('Hodiny')
                    ->state(function (WorkShift $record) {
                        if (!$record->end_at) return '---';
                        return number_format($record->start_at->diffInMinutes($record->end_at) / 60, 2) . ' h';
                    }),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'info',            // Modrá
                        'pending_approval' => 'warning', // Oranžová (POZOR, SCHVÁLIT!)
                        'approved' => 'success',       // Zelená
                        'rejected' => 'danger',        // Červená
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'active' => 'Běží',
                        'pending_approval' => 'Ke schválení',
                        'approved' => 'OK',
                        'rejected' => 'Zamítnuto',
                    }),
            ])
            ->defaultSort('start_at', 'desc')
            ->actions([
                Tables\Actions\EditAction::make(),
                // Rychlé tlačítko "Schválit" přímo v tabulce
                Tables\Actions\Action::make('approve')
                    ->label('Schválit')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->action(function (WorkShift $record) {
                        $record->update(['status' => 'approved']);
                    })
                    ->visible(fn (WorkShift $record) => $record->status === 'pending_approval'),
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