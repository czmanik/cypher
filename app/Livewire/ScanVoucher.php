<?php

namespace App\Livewire;

use App\Models\Voucher;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class ScanVoucher extends Component
{
    public $manualCode = '';

    public function checkCode($code)
    {
        // 1. Najít voucher
        $voucher = Voucher::where('code', $code)->first();

        if (!$voucher) {
            session()->flash('error', "Kód '{$code}' neexistuje!");
            return;
        }

        // 2. Je už použitý?
        if ($voucher->used_at) {
            session()->flash('error', "POZOR: Tento voucher byl už použit " . $voucher->used_at->format('d.m. H:i'));
            return;
        }

        // 3. Aktivovat (spálit) voucher
        $voucher->update([
            'used_at' => now(),
            'used_by_user_id' => Auth::id(),
        ]);

        session()->flash('message', "Voucher v hodnotě {$voucher->value} Kč úspěšně uplatněn! ✅");
        $this->manualCode = ''; // Vymazat pole
    }

    public function render()
    {
        return view('livewire.scan-voucher');
    }
}