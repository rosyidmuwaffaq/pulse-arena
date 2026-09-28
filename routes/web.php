<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'events.index')->name('events.index');
Route::view('/admin/events', 'admin.events.index')->name('admin.events.index');
Route::view('/admin/events/create', 'admin.events.create')->name('admin.events.create');
Route::get('/admin/events/{id}/edit', fn (string $id) => view('admin.events.edit', ['id' => $id]))->name('admin.events.edit');
Route::get('/events/{id}', fn (string $id) => view('events.show', ['id' => $id]))->name('events.show');
Route::view('/tickets', 'tickets.index')->name('tickets.index');
