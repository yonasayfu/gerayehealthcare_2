<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\Staff;
use App\Models\EventBroadcast;
use Illuminate\Database\Seeder;

class EventBroadcastSeeder extends Seeder
{
    public function run(): void
    {
        // Ensure we have some events and staff
        if (Event::count() < 3) {
            Event::factory()->count(3)->create();
        }
        if (Staff::count() < 3) {
            Staff::factory()->count(3)->create();
        }

        // Create sample broadcasts
        EventBroadcast::factory()->count(10)->create();
    }
}
