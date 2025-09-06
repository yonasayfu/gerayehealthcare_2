<?php

namespace Database\Seeders;

use App\Models\Staff;
use App\Models\StaffAvailability;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class StaffAvailabilitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure there are some staff members to assign availabilities to
        if (Staff::count() === 0) {
            Staff::factory()->count(3)->create(); // Limit staff to 3
        }

        $staffIds = Staff::pluck('id');

        foreach ($staffIds as $staffId) {
            // Create 1 or 2 random availability slots for each staff member
            for ($i = 0; $i < rand(1, 2); $i++) {
                $start = Carbon::now()->addDays(rand(1, 30))->startOfDay()->addHours(rand(8, 17));
                $end = $start->copy()->addHours(rand(2, 8));

                StaffAvailability::create([
                    'staff_id' => $staffId,
                    'start_time' => $start,
                    'end_time' => $end,
                    'status' => collect(['Available', 'Booked', 'Unavailable'])->random(),
                ]);
            }
        }
    }
}
