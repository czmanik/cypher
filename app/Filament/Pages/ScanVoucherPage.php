<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\User;

class ScanVoucherPage extends Page
{
    protected static string $view = 'filament.pages.scan-voucher-page';

    protected static ?string $navigationIcon = 'heroicon-o-qr-code';
    protected static ?string $navigationLabel = 'Načíst Voucher';
    protected static ?string $title = 'Skener Voucherů';
    protected static ?int $navigationSort = 10;

    public static function canAccess(): bool
    {
        $user = auth()->user();

        // Pustíme tam každého aktivního zaměstnance
        return $user && $user->is_active;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canAccess();
    }
}
