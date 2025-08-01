<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Event;
use App\Models\EventRecommendation;
use App\Models\EligibilityCriteria;
use App\Models\EventStaffAssignment;

class PagumeCampaignSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $event = Event::create([
            'title' => 'Pagume 2017 Campaign',
            'description' => 'A special campaign for the Pagume 2017 season.',
            'event_date' => '2025-09-06',
            'is_free_service' => true,
            'broadcast_status' => 'Published',
        ]);

        EventRecommendation::factory()->count(6)->create(['event_id' => $event->id]);
        EligibilityCriteria::factory()->count(6)->create(['event_id' => $event->id]);
        EventStaffAssignment::factory()->count(6)->create(['event_id' => $event->id]);
    }
}
