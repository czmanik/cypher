<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Actions\Action;

class Scanner extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-qr-code';
    protected static ?string $navigationLabel = 'Skener';
    protected static ?string $title = 'Skener kódů';
    protected static ?int $navigationSort = 100;

    protected static string $view = 'filament.pages.scanner';

    public ?string $code = '';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('code')
                    ->label('Naskenujte nebo zadejte kód')
                    ->required()
                    ->autofocus()
                    ->extraInputAttributes(['class' => 'text-2xl text-center']),
            ]);
    }

    // Since we want a simple action, we can use a custom view form submission or Livewire action
    // Let's use a simple Livewire method triggered by "Enter" or button

    public function submitCode()
    {
        $code = $this->code;

        if (empty($code)) {
            return;
        }

        // Placeholder logic: Check if it looks like a valid code
        // In real implementation, check Voucher::where('code', $code)->first()

        Notification::make()
            ->title('Kód naskenován')
            ->body("Hodnota: {$code}")
            ->success()
            ->send();

        // Clear
        $this->reset('code');
    }
}
