<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LoginLogsRelationManager extends RelationManager
{
    protected static string $relationship = 'loginLogs';
    protected static ?string $title = 'Historie přihlášení';

    public function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('login_at')
            ->columns([
                Tables\Columns\TextColumn::make('login_at')
                    ->label('Čas')
                    ->dateTime('d.m.Y H:i:s')
                    ->sortable(),

                Tables\Columns\TextColumn::make('ip_address')
                    ->label('IP Adresa')
                    ->searchable(),

                Tables\Columns\TextColumn::make('user_agent')
                    ->label('Zařízení / Prohlížeč')
                    ->limit(50)
                    ->tooltip(fn ($state) => $state),
            ])
            ->defaultSort('login_at', 'desc')
            ->filters([
                //
            ])
            ->headerActions([
                //
            ])
            ->actions([
                //
            ])
            ->bulkActions([
                //
            ]);
    }
}
