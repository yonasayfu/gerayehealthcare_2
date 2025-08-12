<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Staff;
use App\Models\StaffAvailability;
use Carbon\Carbon;

class StaffAvailabilityDemoSeeder extends Seeder
{
    public function run(): void
    {
        // Ensure we have some staff
        $staffMembers = Staff::select('id', 'first_name', 'last_name')->take(10)->get();
        if ($staffMembers->isEmpty()) {
            // If no staff, nothing to seed here
            return;
        }

        // Base date window: tomorrow
        $day = Carbon::tomorrow()->startOfDay();

        // For first 3 staff: create non-overlapping morning/afternoon slots (Available)
        foreach ($staffMembers->take(3) as $staff) {
            StaffAvailability::updateOrCreate(
                [
                    'staff_id' => $staff->id,
                    'start_time' => $day->copy()->addHours(9),
                    'end_time' => $day->copy()->addHours(12),
                ],
                [
                    'status' => 'Available',
                ]
            );

            StaffAvailability::updateOrCreate(
                [
                    'staff_id' => $staff->id,
                    'start_time' => $day->copy()->addHours(13),
                    'end_time' => $day->copy()->addHours(17),
                ],
                [
                    'status' => 'Available',
                ]
            );
        }

        // For next 2 staff: create overlapping slots to test prevention
        foreach ($staffMembers->slice(3, 2) as $staff) {
            // Slot A: 10:00 - 14:00
            StaffAvailability::updateOrCreate(
                [
                    'staff_id' => $staff->id,
                    'start_time' => $day->copy()->addHours(10),
                    'end_time' => $day->copy()->addHours(14),
                ],
                [
                    'status' => 'Available',
                ]
            );
            // Slot B (overlaps): 12:00 - 16:00
            StaffAvailability::updateOrCreate(
                [
                    'staff_id' => $staff->id,
                    'start_time' => $day->copy()->addHours(12),
                    'end_time' => $day->copy()->addHours(16),
                ],
                [
                    'status' => 'Unavailable',
                ]
            );
        }

        // For another 2 staff: mark Unavailable for entire day to test filtering out
        foreach ($staffMembers->slice(5, 2) as $staff) {
            StaffAvailability::updateOrCreate(
                [
                    'staff_id' => $staff->id,
                    'start_time' => $day->copy()->addHours(0),
                    'end_time' => $day->copy()->addHours(23),
                ],
                [
                    'status' => 'Unavailable',
                ]
            );
        }
    }
}
