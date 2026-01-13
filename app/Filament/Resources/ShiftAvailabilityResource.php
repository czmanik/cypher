<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ShiftAvailabilityResource\Pages;
use App\Models\ShiftAvailability;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class ShiftAvailabilityResource extends Resource
{
    protected static ?string $model = ShiftAvailability::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationLabel = 'Moje dostupnost';
    protected static ?string $pluralModelLabel = 'Dostupnost';
    protected static ?string $modelLabel = 'Dostupnost';
    protected static ?string $navigationGroup = 'Moje Práce';

    protected static ?int $navigationSort = 2;

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        // Users see only their own availability. Managers see all (or maybe just via reports/calendars).
        // Let's assume this Resource is primarily for users to INPUT their data.
        // Managers will likely view it via the Shift Calendar.
        // So we filter by current user unless they are manager?
        // Actually, let's keep it simple: Everyone sees their own here.

        $query = parent::getEloquentQuery();

        if (! Auth::user()->is_manager) {
            $query->where('user_id', Auth::id());
        }

        return $query;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Hidden::make('user_id')
                    ->default(fn () => Auth::id()),

                Forms\Components\Grid::make(2)->schema([
                    Forms\Components\DatePicker::make('start_date')
                        ->label('Od')
                        ->required()
                        ->default(now()),

                    Forms\Components\DatePicker::make('end_date')
                        ->label('Do')
                        ->required()
                        ->default(now()->endOfMonth())
                        ->afterOrEqual('start_date'),
                ]),

                Forms\Components\Textarea::make('note')
                    ->label('Poznámka (např. časová omezení)')
                    ->columnSpanFull()
                    ->placeholder('Např. Celý den ok, kromě úterý odpoledne.'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Zaměstnanec')
                    ->visible(fn () => Auth::user()->is_manager)
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('start_date')
                    ->label('Od')
                    ->date('d.m.Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('end_date')
                    ->label('Do')
                    ->date('d.m.Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('note')
                    ->label('Poznámka')
                    ->limit(50),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListShiftAvailabilities::route('/'),
            'create' => Pages\CreateShiftAvailability::route('/create'),
            'edit' => Pages\EditShiftAvailability::route('/{record}/edit'),
        ];
    }
}
