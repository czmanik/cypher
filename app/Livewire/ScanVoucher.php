<?php

namespace App\Livewire;

use App\Models\Voucher;
use App\Models\EventClaim;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class ScanVoucher extends Component
{
    public $manualCode = '';

    public function checkCode($code)
    {
        $code = trim($code);

        // 1. Najít Voucher
        $voucher = Voucher::where('code', $code)->first();

        if ($voucher) {
            // Je už použitý?
            if ($voucher->used_at) {
                session()->flash('error', "POZOR: Tento voucher byl už použit " . $voucher->used_at->format('d.m. H:i'));
                return;
            }

            // Aktivovat (spálit) voucher
            $voucher->update([
                'used_at' => now(),
                'used_by_user_id' => Auth::id(),
            ]);

            session()->flash('message', "Voucher v hodnotě {$voucher->value} Kč úspěšně uplatněn! ✅");
            $this->manualCode = '';
            return;
        }

        // 2. Najít EventClaim (Sleva z akce)
        $claim = EventClaim::where('claim_token', $code)
            ->orWhere('code', $code) // Case sensitive? SQL usually insensitive depending on collation
            ->first();

        if ($claim) {
            // Je už uplatněná?
            if ($claim->redeemed_at) {
                session()->flash('error', "POZOR: Tato sleva byla už uplatněna " . $claim->redeemed_at->format('d.m. H:i'));
                return;
            }

            // Uplatnit slevu
            $claim->update([
                'redeemed_at' => now(),
            ]);

            $eventName = $claim->event ? $claim->event->name : 'Akce';
            session()->flash('message', "Sleva pro '{$eventName}' úspěšně uplatněna! ✅");
            $this->manualCode = '';
            return;
        }

        // 3. Nenalezeno
        session()->flash('error', "Kód '{$code}' neexistuje!");
    }

    public function render()
    {
        return view('livewire.scan-voucher');
    }
}