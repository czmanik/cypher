<?php

namespace App\Filament\Pages;

use App\Settings\SeoSettings;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;
use Filament\Panel;

class ManageSeo extends SettingsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-globe-alt';
    protected static ?string $navigationGroup = 'Obsah webu';
    protected static ?string $navigationLabel = 'Globální SEO';
    protected static ?string $title = 'Globální SEO Nastavení';

    protected static string $settings = SeoSettings::class;

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
                Forms\Components\Section::make('Základní nastavení webu')
                    ->description('Tyto hodnoty se použijí, pokud konkrétní stránka nemá vyplněné vlastní SEO.')
                    ->schema([
                        Forms\Components\TextInput::make('site_name')
                            ->label('Název webu')
                            ->required(),

                        Forms\Components\TextInput::make('title_separator')
                            ->label('Oddělovač titulku')
                            ->default('|')
                            ->required(),

                        Forms\Components\TextInput::make('title_suffix')
                            ->label('Přípona titulku')
                            ->helperText('Např. "Cypher93" -> "Kontakt | Cypher93"'),

                        Forms\Components\Textarea::make('site_description')
                            ->label('Výchozí popis (Meta Description)')
                            ->rows(3),

                        Forms\Components\FileUpload::make('site_image')
                            ->label('Výchozí OG Obrázek (Sdílení)')
                            ->image()
                            ->directory('seo')
                            ->helperText('Použije se při sdílení na sociálních sítích, pokud stránka nemá vlastní obrázek.'),

                        Forms\Components\Select::make('robots_default')
                            ->label('Výchozí Robots')
                            ->options([
                                'index, follow' => 'Index, Follow (Doporučeno)',
                                'noindex, nofollow' => 'Noindex, Nofollow (Skrýt před vyhledávači)',
                            ])
                            ->default('index, follow')
                            ->required(),
                    ]),
            ]);
    }
}
