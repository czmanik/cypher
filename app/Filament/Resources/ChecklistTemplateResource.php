<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ChecklistTemplateResource\Pages;
use App\Models\ChecklistTemplate;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Get; // Důležité pro dynamické schovávání polí

class ChecklistTemplateResource extends Resource
{
    protected static ?string $model = ChecklistTemplate::class;
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static ?string $navigationLabel = 'Šablony Checklistů';
    protected static ?string $navigationGroup = 'HR & Provoz';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('task_name')
                    ->label('Název úkolu')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),

                // SEKCE CÍLENÍ (Kdo to vidí?)
                Forms\Components\Section::make('Kdo má tento úkol vidět?')
                    ->schema([
                        Forms\Components\Select::make('target_type')
                            ->label('Cílení')
                            ->options([
                                'all' => 'Všichni zaměstnanci',
                                'type' => 'Konkrétní oddělení',
                                'user' => 'Konkrétní zaměstnanec',
                            ])
                            ->default('all')
                            ->live() // Reaguje okamžitě
                            ->required(),

                        // Zobrazit jen když je vybráno "type"
                        Forms\Components\Select::make('target_employee_type')
                            ->label('Vyberte oddělení')
                            ->options([
                                'manager' => 'Management / Majitel',
                                'kitchen' => 'Kuchyň',
                                'floor' => 'Plac / Bar',
                                'support' => 'Pomocný personál',
                            ])
                            ->visible(fn (Get $get) => $get('target_type') === 'type')
                            ->required(fn (Get $get) => $get('target_type') === 'type'),

                        // Zobrazit jen když je vybráno "user"
                        Forms\Components\Select::make('target_user_id')
                            ->label('Vyberte zaměstnance')
                            ->relationship('targetUser', 'name')
                            ->searchable()
                            ->preload()
                            ->visible(fn (Get $get) => $get('target_type') === 'user')
                            ->required(fn (Get $get) => $get('target_type') === 'user'),
                    ])->columns(3),

                Forms\Components\Grid::make(2)->schema([
                    Forms\Components\Toggle::make('is_required')
                        ->label('Povinné')
                        ->default(true)
                        ->helperText('Zaměstnanec musí tento úkol splnit, aby mohl zavřít směnu.'),
                    
                    Forms\Components\Toggle::make('is_active')
                        ->label('Aktivní')
                        ->default(true),
                ]),
                
                Forms\Components\TextInput::make('sort_order')
                    ->label('Pořadí')
                    ->numeric()
                    ->default(0),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('task_name')
                    ->label('Úkol')
                    ->searchable(),
                
                // Sloupec pro Cílení (Hezký výpis)
                Tables\Columns\TextColumn::make('target_type')
                    ->label('Cílení')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'all' => 'gray',
                        'type' => 'info',
                        'user' => 'warning',
                    })
                    ->formatStateUsing(fn ($state, $record) => match ($state) {
                        'all' => 'Všichni',
                        'type' => 'Oddělení: ' . match($record->target_employee_type) {
                            'manager' => 'Management', 'kitchen' => 'Kuchyň', 'floor' => 'Plac', 'support' => 'Pomoc', default => ''
                        },
                        'user' => 'Osoba: ' . ($record->targetUser->name ?? 'Neznámý'),
                        default => $state,
                    }),

                Tables\Columns\IconColumn::make('is_required')
                    ->label('Povinné')
                    ->boolean(),
                
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktivní')
                    ->boolean(),
                
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Pořadí')
                    ->sortable(),
            ])
            ->defaultSort('sort_order', 'asc')
            ->reorderable('sort_order')
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListChecklistTemplates::route('/'),
            'create' => Pages\CreateChecklistTemplate::route('/create'),
            'edit' => Pages\EditChecklistTemplate::route('/{record}/edit'),
        ];
    }
}