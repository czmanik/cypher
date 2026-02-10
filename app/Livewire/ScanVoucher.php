<?php

namespace App\Livewire;

use App\Models\Voucher;
use App\Models\EventClaim;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class ScanVoucher extends Component
{
    public $manualCode = '';

    // Stav skenování
    public $scannedType = null; // 'voucher' | 'claim'
    public $scannedId = null;
    public $scannedData = []; // Data pro zobrazení (název, hodnota, email...)

    // Formulář pro potvrzení
    public $staffNote = '';

    public function checkCode($code)
    {
        // When form submits, $code might be passed as argument if we use wire:submit="checkCode(manualCode)"
        // But if we call it from scanner, we pass the scanned text.
        // If the form submits, it passes the value of manualCode property if bound?
        // No, checkCode(manualCode) passes the Alpine/JS value?
        // Wait, wire:submit="checkCode(manualCode)" - manualCode here refers to the Livewire property?
        // No, in Blade `manualCode` is treated as a JS variable if not $manualCode.
        // BUT wire:model="manualCode" updates the backend property.

        // Let's simplify. If we use wire:submit="submitManual", we can use $this->manualCode inside.

        if (empty($code) && !empty($this->manualCode)) {
            $code = $this->manualCode;
        }

        $code = trim((string) $code);
        $this->resetErrorBag();
        $this->reset('scannedType', 'scannedId', 'scannedData', 'staffNote');

        // 1. Najít Voucher
        $voucher = Voucher::where('code', $code)->first();

        if ($voucher) {
            if ($voucher->used_at) {
                $this->addError('manualCode', "POZOR: Tento voucher byl už použit " . $voucher->used_at->format('d.m. H:i'));
                return;
            }

            $this->scannedType = 'voucher';
            $this->scannedId = $voucher->id;
            $this->scannedData = [
                'title' => 'Hodnotový Voucher',
                'subtitle' => $voucher->value . ' Kč',
                'code' => $voucher->code,
                'info' => 'Sleva na útratu',
            ];
            return;
        }

        // 2. Najít EventClaim (Sleva z akce)
        // Hledáme podle claim_token (QR) nebo code (4 znaky)
        $claim = EventClaim::where('claim_token', $code)
            ->orWhere('code', $code)
            ->with('event')
            ->first();

        if ($claim) {
            if ($claim->redeemed_at) {
                $this->addError('manualCode', "POZOR: Tato sleva byla už uplatněna " . $claim->redeemed_at->format('d.m. H:i'));
                return;
            }

            $this->scannedType = 'claim';
            $this->scannedId = $claim->id;
            $this->scannedData = [
                'title' => $claim->event ? $claim->event->title : 'Neznámá akce',
                'subtitle' => $claim->email,
                'code' => $claim->code,
                'info' => $claim->phone . ($claim->instagram ? ' | ' . $claim->instagram : ''),
            ];

            // Předvyplnit poznámku, pokud už nějaká existuje (což by neměla u neuplatněného, ale pro jistotu)
            $this->staffNote = $claim->staff_note ?? '';

            return;
        }

        // 3. Nenalezeno
        $this->addError('manualCode', "Kód '{$code}' neexistuje!");
    }

    // Wrapper for manual submission to ensure property usage
    public function submitManual() {
        $this->checkCode($this->manualCode);
    }

    public function confirmRedemption()
    {
        if ($this->scannedType === 'voucher') {
            $voucher = Voucher::find($this->scannedId);
            if ($voucher && !$voucher->used_at) {
                $voucher->update([
                    'used_at' => now(),
                    'used_by_user_id' => Auth::id(),
                ]);
                session()->flash('message', "Voucher {$voucher->value} Kč úspěšně uplatněn! ✅");
            }
        } elseif ($this->scannedType === 'claim') {
            $claim = EventClaim::find($this->scannedId);
            if ($claim && !$claim->redeemed_at) {
                $claim->update([
                    'redeemed_at' => now(),
                    'staff_note' => $this->staffNote,
                ]);
                session()->flash('message', "Voucher na akci '{$this->scannedData['title']}' úspěšně uplatněn! ✅");
            }
        }

        $this->resetScanner();
    }

    public function cancelRedemption()
    {
        $this->resetScanner();
    }

    public function resetScanner()
    {
        $this->reset('manualCode', 'scannedType', 'scannedId', 'scannedData', 'staffNote');
    }

    public function render()
    {
        return view('livewire.scan-voucher');
    }
}
