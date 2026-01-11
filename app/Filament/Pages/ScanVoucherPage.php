<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class ScanVoucherPage extends Page
{
    protected static string $view = 'filament.pages.scan-voucher-page';

    protected static ?string $navigationIcon = 'heroicon-o-qr-code';
    protected static ?string $navigationLabel = 'Načíst Voucher';
    protected static ?string $title = 'Skener Voucherů';
}
