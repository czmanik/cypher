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
                ->color('success')
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

            Action::make('importMenu')
                ->label('Importovat Menu')
                ->icon('heroicon-o-cloud-arrow-down')
                ->color('primary')
                ->requiresConfirmation()
                ->modalHeading('Importovat Menu ze Storyous')
                ->modalDescription('Tato akce stáhne kategorie a produkty. Nové položky budou skryté. Existující položky se aktualizují (název, cena).')
                ->action(function (StoryousService $service) {
                    $result = $service->importMenu();

                    if (isset($result['status']) && $result['status'] === 'success') {
                        $stats = $result['stats'];
                        Notification::make()
                            ->title('Menu importováno')
                            ->body("Kategorie: +{$stats['categories_created']} (upraveno {$stats['categories_updated']})\nProdukty: +{$stats['products_created']} (upraveno {$stats['products_updated']})\nPřejmenováno starých: {$stats['products_renamed_old']}")
                            ->success()
                            ->persistent()
                            ->send();
                    } else {
                        Notification::make()
                            ->title('Chyba importu')
                            ->body($result['message'] ?? 'Neznámá chyba')
                            ->danger()
                            ->send();
                    }
                }),

            Action::make('syncBills')
                ->label('Synchronizovat Účtenky')
                ->icon('heroicon-o-arrow-path')
                ->color('warning')
                ->modalHeading('Synchronizace účtenek')
                ->modalContent(view('filament.pages.storyous-sync-modal'))
                ->modalSubmitAction(false) // Disable default footer actions since Livewire handles it
                ->modalCancelAction(false),
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
                            ->label('Client Secret (Secret)')
                            ->password()
                            ->revealable()
                            ->helperText('Tajný klíč aplikace'),

                        Forms\Components\TextInput::make('merchant_id')
                            ->label('Merchant ID')
                            ->helperText('ID obchodníka ve Storyous'),

                        Forms\Components\TextInput::make('place_id')
                            ->label('Place ID')
                            ->helperText('Identifikátor provozovny (nahrazuje původní API Key field)'),
                    ]),

                Forms\Components\Section::make('Nastavení synchronizace')
                    ->description('Konfigurace pro stahování dat.')
                    ->schema([
                        Forms\Components\DatePicker::make('sync_start_date')
                            ->label('Datum začátku synchronizace')
                            ->helperText('Datum, od kterého se budou stahovat účtenky. Pokud je prázdné, použije se výchozí (poslední měsíc).')
                            ->required(),
                    ]),
            ]);
    }
}
