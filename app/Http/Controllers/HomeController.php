<?php

namespace App\Http\Controllers;

use App\Models\ContentBlock;
use App\Models\Event;
use App\Models\OpeningHour;
use Carbon\Carbon;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function __invoke()
    {
        // 1. Zjistíme aktuální den a čas (Beze změny)
        $now = \Carbon\Carbon::now();
        $today = OpeningHour::where('day_of_week', $now->dayOfWeekIso)->first();

        // 2. Otevírací doba (Beze změny)
        $isOpen = false;
        if ($today && !$today->is_closed) {
            $open = \Carbon\Carbon::parse($today->bar_open);
            $close = \Carbon\Carbon::parse($today->bar_close);
            
            if ($close->lessThan($open)) {
                $close->addDay();
            }
            
            $isOpen = $now->between($open, $close);
        }

        // 3. Vibe - Den vs Noc (Beze změny)
        $isNight = $now->hour >= 18;

        // 4. Obsah z Adminu (Beze změny)
        $heroBlock = ContentBlock::where('key', 'homepage_hero')->first();
        $aboutBlock = ContentBlock::where('key', 'about_us')->first();

        // ==========================================
        // 5. NOVÁ LOGIKA PRO AKCE (Tabulky a počty)
        // ==========================================

        // A) Definujeme základní pravidlo: "Co je aktuální?"
        // (Tj. akce co začínají v budoucnu NEBO co už běží a ještě neskončily)
        $baseQuery = Event::where('is_published', true)
            ->where(function ($query) use ($now) {
                $query->where('start_at', '>=', $now)
                      ->orWhere(function ($q) use ($now) {
                          $q->where('start_at', '<', $now)
                            ->where(function ($endQ) use ($now) {
                                $endQ->where('end_at', '>=', $now)
                                     ->orWhereNull('end_at');
                            });
                      });
            });

        // B) Spočítáme počty pro jednotlivé záložky (pro čísla v závorkách)
        // Používáme 'clone', abychom si nezničili původní dotaz pro další použití
        $counts = [
            'all' => (clone $baseQuery)->count(),
            'kultura' => (clone $baseQuery)->where('category', 'kultura')->count(),
            'gastro' => (clone $baseQuery)->where('category', 'gastro')->count(),
            'piti' => (clone $baseQuery)->where('category', 'piti')->count(),
        ];

        // C) Načteme samotné seznamy (vždy max 3 nejbližší pro každou sekci)
        $eventsLists = [
            'all' => (clone $baseQuery)->orderBy('start_at', 'asc')->take(3)->get(),
            'kultura' => (clone $baseQuery)->where('category', 'kultura')->orderBy('start_at', 'asc')->take(3)->get(),
            'gastro' => (clone $baseQuery)->where('category', 'gastro')->orderBy('start_at', 'asc')->take(3)->get(),
            'piti' => (clone $baseQuery)->where('category', 'piti')->orderBy('start_at', 'asc')->take(3)->get(),
        ];

        // 6. Odeslání do View (všimni si, že posíláme $counts a $eventsLists místo starého $events)
        return view('welcome', compact('isOpen', 'isNight', 'heroBlock', 'aboutBlock', 'today', 'counts', 'eventsLists'));
    }
}