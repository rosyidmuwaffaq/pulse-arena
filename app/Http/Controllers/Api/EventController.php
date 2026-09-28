<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEventRequest;
use App\Models\Event;
use Illuminate\Http\JsonResponse;

class EventController extends Controller
{
    public function index(): JsonResponse
    {
        $events = Event::withCount('tickets')
            ->orderBy('event_date')
            ->get();

        return response()->json(['data' => $events]);
    }

    public function store(StoreEventRequest $request): JsonResponse
    {
        $event = Event::create($request->validated());

        return response()->json(['data' => $event], 201);
    }

    public function show(Event $event): JsonResponse
    {
    $event->loadCount('tickets');

    return response()->json(['data' => $event]);
    }

    public function destroy(Event $event): JsonResponse
    {
    if ($event->tickets()->exists()) {
        return response()->json([
            'message' => 'Event tidak bisa dihapus karena sudah memiliki tiket.',
        ], 409);
    }

    $event->delete();

    return response()->json(null, 204);
    }
}