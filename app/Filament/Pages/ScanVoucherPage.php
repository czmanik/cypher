<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\User; // Doplnil jsem import modelu, ať to máš čisté

class ScanVoucherPage extends Page
{
    protected static string $view = 'filament.pages.scan-voucher-page';

    protected static ?string $navigationIcon = 'heroicon-o-qr-code';
    protected static ?string $navigationLabel = 'Načíst Voucher';
    protected static ?string $title = 'Skener Voucherů';

    // --- Metoda musí být UVNITŘ třídy ---

    public static function canAccess(): bool
    {
        // 1. Získáme přihlášeného uživatele
        $user = auth()->user();

        if (!$user) return false;

        // 2. Pokud je manažer, může vždy
        if ($user->is_manager) return true;

        // 3. Pustíme tam Kuchaře a Plac
        // (Používám self:: konstantu z modelu User, je to bezpečnější než psát texty ručně)
        return in_array($user->employee_type, [
            User::TYPE_KITCHEN, 
            User::TYPE_FLOOR,
            // User::TYPE_MANAGER, // To už jsme pokryli nahoře, ale nevadí to tu
        ]);
    }
} 