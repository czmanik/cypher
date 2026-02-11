<?php

namespace App\Filament\Pages;

use App\Settings\StoryousSettings;
use App\Services\StoryousService;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\SettingsPage;

class ManageStoryous extends SettingsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-cog';
    protected static ?string $navigationGroup = 'Nastavení';
    protected static ?string $navigationLabel = 'Storyous API';
    protected static ?string $title = 'Nastavení Storyous API';
    protected static ?int $navigationSort = 100;

    protected static string $settings = StoryousSettings::class;

    public static function canAccess(): bool
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        // Only accessible by admins
        return $user && $user->is_admin;
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('testConnection')
                ->label('Ověřit spojení')
                ->icon('heroicon-o-check-circle')
                ->color('success') // Green button
                ->action(function (StoryousService $service) {
                    if ($service->testConnection()) {
                        Notification::make()
                            ->title('Spojení navázáno')
                            ->body('API klíče jsou platné a služba je dostupná.')
                            ->success()
                            ->send();
                    } else {
                        Notification::make()
                            ->title('Chyba spojení')
                            ->body('Nepodařilo se připojit k API. Zkontrolujte klíče.')
                            ->danger()
                            ->send();
                    }
                }),
        ];
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('API Přístupové údaje')
                    ->description('Zadejte klíče pro komunikaci se systémem Storyous.')
                    ->schema([
                        Forms\Components\TextInput::make('client_id')
                            ->label('Client ID')
                            ->helperText('Identifikátor aplikace'),

                        Forms\Components\TextInput::make('client_secret')
                            ->label('Client Secret')
                            ->password()
                            ->revealable()
                            ->helperText('Tajný klíč aplikace'),

                        Forms\Components\TextInput::make('api_key')
                            ->label('API Key')
                            ->password()
                            ->revealable()
                            ->helperText('Alternativní API klíč (pokud je používán místo OAuth)'),

                        Forms\Components\TextInput::make('merchant_id')
                            ->label('Merchant ID')
                            ->helperText('ID obchodníka ve Storyous'),
                    ]),
            ]);
    }
}
