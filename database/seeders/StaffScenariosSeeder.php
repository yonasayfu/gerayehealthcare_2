<?php

namespace Database\Seeders;

use App\Models\Staff;
use App\Models\StaffAvailability;
use Illuminate\Database\Seeder;

class StaffScenariosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $staff = Staff::all();

        // Create availability for the staff
        $staff->each(function ($staffMember) {
            // One available slot
            StaffAvailability::factory()->available()->create([
                'staff_id' => $staffMember->id,
            ]);

            // One unavailable slot
            StaffAvailability::factory()->unavailable()->create([
                'staff_id' => $staffMember->id,
            ]);
        });
    }
}
