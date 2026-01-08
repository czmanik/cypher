<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    // Zobrazení formuláře
    public function create()
    {
        return view('reservations.create');
    }

    // Uložení rezervace
    public function store(Request $request)
    {
        // 1. Validace dat od uživatele
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email',
            'phone' => 'required|string|max:20',
            'date' => 'required|date|after:today', // Musí být v budoucnu
            'time' => 'required',
            'guests' => 'required|integer|min:1|max:20',
            'note' => 'nullable|string',
        ]);

        // 2. Vytvoření data a času (spojíme datum a čas do jednoho sloupce)
        $reservationTime = $validated['date'] . ' ' . $validated['time'];

        // 3. Uložení do databáze
        Reservation::create([
            'customer_name' => $validated['name'],
            'customer_email' => $validated['email'],
            'customer_phone' => $validated['phone'],
            'reservation_time' => $reservationTime,
            'guests_count' => $validated['guests'],
            'note' => $validated['note'],
            'status' => 'pending', // Výchozí stav: Čeká na potvrzení
            'table_id' => null,    // Stůl zatím není přidělen
        ]);

        // 4. Přesměrování s hláškou
        return redirect()->route('home')->with('success', 'Rezervace byla odeslána! Potvrdíme ji SMSkou nebo e-mailem.');
    }
}