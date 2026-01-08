<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    // Seznam nadcházejících akcí
    public function index(Request $request)
    {
        $now = now();
        $filter = $request->query('category'); // Získáme kategorii z URL (?category=...)

        // Základní query pro publikované akce
        $query = Event::where('is_published', true);

        // Pokud je vybrána kategorie, filtrujeme
        if ($filter && in_array($filter, ['kultura', 'gastro', 'piti'])) {
            $query->where('category', $filter);
        }

        // Provedeme query jen jednou a výsledky si roztřídíme v PHP (je to efektivnější pro menší weby)
        // Seřadíme podle začátku
        $allEvents = $query->orderBy('start_at', 'asc')->get();

        // Roztřídění do 3 kbelíků
        $runningEvents = $allEvents->filter(function ($event) use ($now) {
            // Probíhá: Začalo v minulosti A (ještě neskončilo NEBO nemá konec)
            return $event->start_at <= $now && ($event->end_at >= $now || is_null($event->end_at));
        });

        $upcomingEvents = $allEvents->filter(function ($event) use ($now) {
            // Budoucí: Teprve začne
            return $event->start_at > $now;
        });

        // Pro minulé akce uděláme raději separátní query s jiným řazením (od nejnovějších)
        $pastEvents = Event::where('is_published', true)
            ->where(function ($q) use ($now) {
                $q->where('end_at', '<', $now)
                ->orWhere(function ($q2) use ($now) {
                    $q2->whereNull('end_at')->where('start_at', '<', $now);
                });
            })
            ->when($filter, fn($q) => $q->where('category', $filter)) // Aplikovat filtr i na archiv
            ->orderBy('start_at', 'desc') // Archiv řadíme opačně (nejnovější nahoře)
            ->get();

        return view('events.index', compact('runningEvents', 'upcomingEvents', 'pastEvents', 'filter'));
    }

    // Detail konkrétní akce (podle URL slugu)
    public function show($slug)
    {
        $event = Event::where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail(); // Pokud nenajde, hodí chybu 404

        return view('events.show', compact('event'));
    }
}