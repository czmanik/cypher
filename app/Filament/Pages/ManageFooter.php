<?php

namespace App\Filament\Pages;

use App\Settings\FooterSettings;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;

class ManageFooter extends SettingsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-bars-3-bottom-left';
    protected static ?string $navigationGroup = 'Obsah webu';
    protected static ?string $navigationLabel = 'Nastavení Patičky';
    protected static ?string $title = 'Nastavení Patičky';

    protected static string $settings = FooterSettings::class;

    public static function canAccess(): bool
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        return $user && $user->is_manager;
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Footer Settings')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Měřící kódy')
                            ->schema([
                                Forms\Components\Textarea::make('measuring_code')
                                    ->label('Měřící kód (Google Analytics, GTM)')
                                    ->helperText('Vložte celý script včetně <script> tagů. Bude vložen do hlavičky webu.')
                                    ->rows(10),
                            ]),

                        Forms\Components\Tabs\Tab::make('Rozložení a Obsah')
                            ->schema([
                                Forms\Components\Grid::make(3)
                                    ->schema([
                                        Forms\Components\Select::make('column_left_type')
                                            ->label('Levý sloupec')
                                            ->options([
                                                'text' => 'Vlastní text',
                                                'opening_hours' => 'Otevírací doba',
                                                'socials' => 'Sociální sítě',
                                                'none' => 'Skrýt',
                                            ])
                                            ->default('text')
                                            ->live(),

                                        Forms\Components\Select::make('column_center_type')
                                            ->label('Prostřední sloupec')
                                            ->options([
                                                'text' => 'Vlastní text',
                                                'opening_hours' => 'Otevírací doba',
                                                'socials' => 'Sociální sítě',
                                                'none' => 'Skrýt',
                                            ])
                                            ->default('opening_hours')
                                            ->live(),

                                        Forms\Components\Select::make('column_right_type')
                                            ->label('Pravý sloupec')
                                            ->options([
                                                'text' => 'Vlastní text',
                                                'opening_hours' => 'Otevírací doba',
                                                'socials' => 'Sociální sítě',
                                                'none' => 'Skrýt',
                                            ])
                                            ->default('socials')
                                            ->live(),
                                    ]),

                                Forms\Components\RichEditor::make('column_left_text')
                                    ->label('Text levého sloupce')
                                    ->visible(fn (Forms\Get $get) => $get('column_left_type') === 'text'),

                                Forms\Components\RichEditor::make('column_center_text')
                                    ->label('Text prostředního sloupce')
                                    ->visible(fn (Forms\Get $get) => $get('column_center_type') === 'text'),

                                Forms\Components\RichEditor::make('column_right_text')
                                    ->label('Text pravého sloupce')
                                    ->visible(fn (Forms\Get $get) => $get('column_right_type') === 'text'),
                            ]),

                        Forms\Components\Tabs\Tab::make('Sociální sítě')
                            ->schema([
                                Forms\Components\Repeater::make('social_links')
                                    ->label('Odkazy na sociální sítě')
                                    ->schema([
                                        Forms\Components\Select::make('network')
                                            ->label('Síť')
                                            ->options([
                                                'facebook' => 'Facebook',
                                                'instagram' => 'Instagram',
                                                'tiktok' => 'TikTok',
                                                'youtube' => 'YouTube',
                                                'twitter' => 'X (Twitter)',
                                                'linkedin' => 'LinkedIn',
                                                'web' => 'Web / Jiné',
                                            ])
                                            ->required()
                                            ->live(),

                                        Forms\Components\TextInput::make('url')
                                            ->label('URL adresa')
                                            ->url()
                                            ->required()
                                            ->prefix('https://'),

                                        Forms\Components\TextInput::make('label')
                                            ->label('Popisek (nepovinné)')
                                            ->visible(fn (Forms\Get $get) => $get('network') === 'web')
                                            ->placeholder('Např. Můj blog'),
                                    ])
                                    ->columns(3)
                                    ->defaultItems(0),
                            ]),

                        Forms\Components\Tabs\Tab::make('Copyright')
                            ->schema([
                                Forms\Components\TextInput::make('copyright_text')
                                    ->label('Text v patičce (Copyright)')
                                    ->default('© 2024 Cypher93. Všechna práva vyhrazena.')
                                    ->required(),
                            ]),
                    ]),
            ]);
    }
}
