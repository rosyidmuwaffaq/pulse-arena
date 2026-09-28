<?php

use App\Http\Controllers\Api\EventController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TicketController;

Route::post('/events/{event}/tickets', [TicketController::class, 'store']);
Route::get('/events', [EventController::class, 'index']);
Route::post('/events', [EventController::class, 'store']);
Route::get('/events/{event}', [EventController::class, 'show']);
Route::delete('/events/{event}', [EventController::class, 'destroy']);