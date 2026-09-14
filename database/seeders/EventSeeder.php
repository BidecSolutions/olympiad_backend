<?php

namespace Database\Seeders;

use App\Enums\EventStatusEnum;
use App\Enums\RolesEnum;
use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::query()
            ->role(RolesEnum::SuperAdmin->value)
            ->first();

        if (! $admin) {
            return;
        }

        $events = [
            [
                'name' => 'Baitussalam Olympiad 2025',
                'sport' => 'General',
                'venue' => 'Main Campus Arena',
                'start' => '1 Sep 2025',
                'end' => '30 Sep 2025',
                'year' => 2025,
            ],
            [
                'name' => 'Inter-School Football Cup',
                'sport' => 'Football',
                'venue' => 'City Stadium',
                'start' => '15 Jun 2025',
                'end' => '18 Jun 2025',
                'year' => 2025,
            ],
            [
                'name' => 'Science Olympiad 2025',
                'sport' => 'Science',
                'venue' => 'Science Block Hall',
                'start' => '20 Jun 2025',
                'end' => '22 Jun 2025',
                'year' => 2025,
            ],
            [
                'name' => 'Art & Culture Festival',
                'sport' => 'Arts',
                'venue' => 'Exhibition Centre',
                'start' => '25 Jun 2025',
                'end' => '27 Jun 2025',
                'year' => 2025,
            ],
            [
                'name' => 'Cricket Championship',
                'sport' => 'Cricket',
                'venue' => 'National Cricket Ground',
                'start' => '5 Jul 2025',
                'end' => '10 Jul 2025',
                'year' => 2025,
            ],
            [
                'name' => 'Debate Competition 2025',
                'sport' => 'Debate',
                'venue' => 'Auditorium',
                'start' => '12 Jul 2025',
                'end' => '14 Jul 2025',
                'year' => 2025,
            ],
        ];

        foreach ($events as $data) {
            Event::query()->updateOrCreate(
                ['name' => $data['name']],
                [
                    'description' => "Sport: {$data['sport']}\nVenue: {$data['venue']}\nDates: {$data['start']} → {$data['end']}",
                    'year' => $data['year'],
                    'status' => EventStatusEnum::Active,
                    'created_by' => $admin->id,
                ],
            );
        }
    }
}
