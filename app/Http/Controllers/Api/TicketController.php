<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTicketRequest;
use App\Models\Event;
use App\Models\Ticket;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TicketController extends Controller
{
    public function store(StoreTicketRequest $request, Event $event): JsonResponse
    {
        return DB::transaction(function () use ($request, $event) {
            $event = Event::lockForUpdate()->findOrFail($event->id);

            if ($event->event_date->isPast()) {
                return response()->json([
                    'message' => 'Event sudah kedaluwarsa, tiket tidak bisa dipesan.',
                ], 422);
            }

            if ($event->tickets()->count() >= $event->quota) {
                return response()->json([
                    'message' => 'Kuota tiket untuk event ini sudah habis.',
                ], 422);
            }

            $ticket = $event->tickets()->create([
                ...$request->validated(),
                'ticket_code' => 'PA-' . strtoupper(Str::random(8)),
            ]);

            return response()->json(['data' => $ticket], 201);
        });
    }

    public function index(Request $request): JsonResponse
    {
        $tickets = Ticket::with('event:id,name,division,event_date,location')
            ->when($request->query('event_id'), fn ($q, $id) => $q->where('event_id', $id))
            ->when($request->query('email'), fn ($q, $email) => $q->where('buyer_email', $email))
            ->when($request->query('status'), fn ($q, $status) => $q->where('status', $status))
            ->latest()
            ->get();

        return response()->json(['data' => $tickets]);
    }
}