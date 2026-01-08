<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\EventController;

Route::get('/', HomeController::class)->name('home');
Route::get('/menu', MenuController::class)->name('menu');
Route::get('/rezervace', [ReservationController::class, 'create'])->name('reservations.create');
Route::post('/rezervace', [ReservationController::class, 'store'])->name('reservations.store');
Route::get('/akce', [EventController::class, 'index'])->name('events.index');
Route::get('/akce/{slug}', [EventController::class, 'show'])->name('events.show');
