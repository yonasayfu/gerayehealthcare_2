<?php

namespace Database\Seeders;

use App\Models\Patient;
use App\Models\Staff;
use App\Models\VisitService;
use Illuminate\Database\Seeder;

class VisitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $patient = Patient::first();
        $staff = Staff::where('position', 'Nurse')->first();

        if ($patient && $staff) {
            VisitService::factory()->create([
                'patient_id' => $patient->id,
                'staff_id' => $staff->id,
                'scheduled_at' => now()->addDays(1),
            ]);

            VisitService::factory()->create([
                'patient_id' => $patient->id,
                'staff_id' => $staff->id,
                'scheduled_at' => now()->addDays(3),
            ]);
        }
    }
}
