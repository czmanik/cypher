<?php

namespace App\Livewire;

use App\Models\Event;
use App\Models\EventClaim;
use Livewire\Component;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Str;

class EventClaimForm extends Component
{
    public Event $event;
    
    // Stavy formuláře
    public bool $showModal = false;
    public bool $success = false;
    public $qrCodeSvg = null;

    // Políčka formuláře
    public $email = '';
    public $phone = '';
    public $instagram = '';

    public function mount(Event $event)
    {
        $this->event = $event;
    }

    public function openModal()
    {
        // Kontrola kapacity
        if ($this->event->remaining_capacity <= 0) {
            return;
        }
        
        $this->showModal = true;
    }

    public function submit()
    {
        // 1. Dynamická validace podle nastavení v Adminu
        $rules = [];
        $requiredFields = $this->event->required_fields ?? [];

        if (in_array('email', $requiredFields)) {
            $rules['email'] = 'required|email';
        }
        if (in_array('phone', $requiredFields)) {
            $rules['phone'] = 'required|min:9';
        }
        if (in_array('instagram', $requiredFields)) {
            $rules['instagram'] = 'required|min:3';
        }

        if (!empty($rules)) {
            $this->validate($rules);
        }

        // 2. Kontrola kapacity těsně před zápisem (pro jistotu)
        if ($this->event->remaining_capacity <= 0) {
            $this->addError('capacity', 'Bohužel, kapacita byla právě vyčerpána.');
            return;
        }

        // 3. Vytvoření nároku (Voucheru)
        $token = Str::random(32); // Unikátní kód pro QR

        EventClaim::create([
            'event_id' => $this->event->id,
            'email' => $this->email,
            'phone' => $this->phone,
            'instagram' => $this->instagram,
            'claim_token' => $token,
            //'claimed_at' => now(),
        ]);

        // 4. Vygenerování QR kódu
        // PŘIDAT (string) PŘED VOLÁNÍ FUNKCE
        $this->qrCodeSvg = (string) QrCode::size(200)
            ->color(0, 0, 0)
            ->generate($token);

        $this->success = true;
        $this->showModal = false;
    }

    public function render()
    {
        return view('livewire.event-claim-form');
    }
}