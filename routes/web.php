<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\PageController;
use App\Models\WorkShift;

Route::get('/', HomeController::class)->name('home');
Route::get('/menu', MenuController::class)->name('menu');
Route::get('/rezervace', [ReservationController::class, 'create'])->name('reservations.create');
Route::post('/rezervace', [ReservationController::class, 'store'])->name('reservations.store');
Route::get('/akce', [EventController::class, 'index'])->name('events.index');
Route::get('/akce/{slug}', [EventController::class, 'show'])->name('events.show');

// Print Route for Work Shifts
Route::get('/admin/work-shifts/{record}/print', function (WorkShift $record) {
    if (! auth()->check()) {
        return redirect()->route('filament.admin.auth.login');
    }
    if (! auth()->user()->is_manager && auth()->id() !== $record->user_id) {
        abort(403);
    }
    return view('filament.pages.work-shift-print', ['record' => $record]);
})->middleware('web')->name('work-shifts.print');

Route::get('/{slug}', [PageController::class, 'show'])->name('page.show');
