<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'events.index')->name('events.index');
Route::view('/events/create', 'events.create')->name('events.create');
Route::get('/events/{id}', fn (string $id) => view('events.show', ['id' => $id]))->name('events.show');
Route::view('/tickets', 'tickets.index')->name('tickets.index');