<?php

namespace App\Filament\Resources\WorkShiftResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Spatie\Activitylog\Models\Activity;

class ActivitiesRelationManager extends RelationManager
{
    protected static string $relationship = 'activities';

    protected static ?string $title = 'Historie změn';

    protected static ?string $icon = 'heroicon-o-clipboard-document-list';

    public function isReadOnly(): bool
    {
        return true;
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\KeyValue::make('properties.attributes')
                    ->label('Nové hodnoty'),
                Forms\Components\KeyValue::make('properties.old')
                    ->label('Původní hodnoty'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('description')
            ->columns([
                Tables\Columns\TextColumn::make('causer.name')
                    ->label('Kdo')
                    ->placeholder('Systém / Neznámý'),

                Tables\Columns\TextColumn::make('description')
                    ->label('Akce')
                    ->formatStateUsing(fn (string $state) => ucfirst($state)),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Kdy')
                    ->dateTime('d.m.Y H:i:s')
                    ->sortable(),

                Tables\Columns\TextColumn::make('properties')
                    ->label('Změny')
                    ->formatStateUsing(function (Activity $record) {
                        $changes = [];
                        $attributes = $record->properties['attributes'] ?? [];
                        $old = $record->properties['old'] ?? [];

                        if (empty($attributes) && empty($old)) {
                            return '---';
                        }

                        foreach ($attributes as $key => $newValue) {
                            $oldValue = $old[$key] ?? '—';
                            // Přeskočíme, pokud se nezměnilo (kromě vytvoření, kde old je prázdné)
                            if ($newValue == $oldValue) continue;

                            $changes[] = "$key: $oldValue ➝ $newValue";
                        }

                        return implode(', ', $changes);
                    })
                    ->wrap()
                    ->html(false), // Pro jednoduchost text, nebo můžeme HTML s <br>
            ])
            ->filters([
                //
            ])
            ->headerActions([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Detail'),
            ])
            ->bulkActions([
                //
            ])
            ->defaultSort('created_at', 'desc');
    }
}
