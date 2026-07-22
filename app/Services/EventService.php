<?php

namespace App\Services;

use App\Models\Event;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class EventService
{
    /**
     * @return LengthAwarePaginator<int, Event>
     */
    public function list(int $perPage = 15): LengthAwarePaginator
    {
        return Event::query()
            ->with('creator')
            ->latest()
            ->paginate($perPage);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Event
    {
        unset($data['created_by']);

        $data['created_by'] = Auth::id();

        $event = Event::create($data);

        return $event->load('creator');
    }

    public function find(Event $event): Event
    {
        return $event->load('creator');
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(Event $event, array $data): Event
    {
        unset($data['created_by']);

        if ($data !== []) {
            $event->update($data);
        }

        return $event->fresh()->load('creator');
    }

    public function delete(Event $event): void
    {
        $event->delete();
    }
}
