<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\EventStatusEnum;
use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Services\EventService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EventController extends Controller
{
    public function __construct(private EventService $eventService) {}

    public function index(): JsonResponse
    {
        $events = $this->eventService->list();

        return response()->json([
            'status' => true,
            'message' => 'Events retrieved successfully.',
            'data' => $events,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $payloadData = $request->validate($this->eventPayloadRules());

        $event = $this->eventService->create($payloadData);

        return response()->json([
            'status' => true,
            'message' => 'Event created successfully.',
            'data' => $event,
        ], 201);
    }

    public function show(Event $event): JsonResponse
    {
        return response()->json([
            'status' => true,
            'message' => 'Event retrieved successfully.',
            'data' => $this->eventService->find($event),
        ]);
    }

    public function update(Request $request, Event $event): JsonResponse
    {
        $payloadData = $request->validate($this->eventPayloadRules($event));

        $event = $this->eventService->update($event, $payloadData);

        return response()->json([
            'status' => true,
            'message' => 'Event updated successfully.',
            'data' => $event,
        ]);
    }

    public function destroy(Event $event): JsonResponse
    {
        $this->eventService->delete($event);

        return response()->json([
            'status' => true,
            'message' => 'Event deleted successfully.',
        ]);
    }

    /**
     * @return array<string, list<mixed>>
     */
    private function eventPayloadRules(?Event $event = null): array
    {
        $eventId = $event?->id;

        return [
            'name' => $eventId
                ? ['sometimes', 'required', 'string', 'max:255']
                : ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'start_date' => $eventId
                ? ['sometimes', 'required', 'date']
                : ['required', 'date'],
            'end_date' => $eventId
                ? ['sometimes', 'required', 'date', 'after_or_equal:start_date']
                : ['required', 'date', 'after_or_equal:start_date'],
            'status' => ['nullable', Rule::enum(EventStatusEnum::class)],
        ];
    }
}
