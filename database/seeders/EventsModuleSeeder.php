<?php

namespace Database\Seeders;

use App\Models\EthiopianCalendarDay;
use App\Models\Event;
use App\Models\EventBroadcast;
use App\Models\EventParticipant;
use App\Models\EventRecommendation;
use App\Models\EventStaffAssignment;
use App\Models\Staff;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class EventsModuleSeeder extends Seeder
{
    /**
     * Seed community outreach events, participants, and calendar data.
     */
    public function run(): void
    {
        $staffMembers = Staff::orderBy('id')->take(4)->get();
        if ($staffMembers->isEmpty()) {
            $staffMembers = Staff::factory()->count(4)->create();
        }

        $events = Event::factory()->count(5)->create([
            'event_date' => Carbon::now()->addDays(7),
            'broadcast_status' => 'Published',
        ]);

        foreach ($events as $index => $event) {
            EthiopianCalendarDay::factory()->create([
                'gregorian_date' => Carbon::now()->addDays($index)->toDateString(),
            ]);

            EventParticipant::factory()->count(3)->create([
                'event_id' => $event->id,
                'status' => $index % 2 === 0 ? 'Confirmed' : 'Pending',
            ]);

            EventStaffAssignment::factory()->create([
                'event_id' => $event->id,
                'staff_id' => $staffMembers[$index % $staffMembers->count()]->id,
                'role' => 'Coordinator',
            ]);

            EventBroadcast::factory()->create([
                'event_id' => $event->id,
            ]);

            EventRecommendation::factory()->create([
                'event_id' => $event->id,
            ]);
        }
    }
}
