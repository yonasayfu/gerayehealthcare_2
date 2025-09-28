<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\EventParticipant;
use App\Models\EventRecommendation;
use App\Models\EventStaffAssignment;
use App\Models\Patient;
use Illuminate\Database\Seeder;

class EventModuleSeeder extends Seeder
{
    public function run(): void
    {
        // Ensure some events exist
        $events = Event::factory()->count(5)->create();

        foreach ($events as $event) {
            // Recommendations
            EventRecommendation::factory()->count(3)->create([
                'event_id' => $event->id,
            ]);
            // Staff assignments
            EventStaffAssignment::factory()->count(3)->create([
                'event_id' => $event->id,
            ]);
            // Participants using existing patients
            $patientIds = Patient::inRandomOrder()->limit(5)->pluck('id');
            foreach ($patientIds as $pid) {
                EventParticipant::factory()->create([
                    'event_id' => $event->id,
                    'patient_id' => $pid,
                ]);
            }
        }
    }
}
