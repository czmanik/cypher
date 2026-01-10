<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\View\View;

class PageController extends Controller
{
    /**
     * Univerzální metoda pro zobrazení stránky podle slugu.
     * Funguje pro /o-nas, /kontakt, /kariera...
     */
    public function show(string $slug): View
    {
        $page = Page::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail(); // Hodí 404, pokud neexistuje

        return view('pages.show', compact('page'));
    }
}