<?php

namespace Database\Seeders;

use App\Models\Patient;
use App\Models\Staff;
use App\Models\VisitService;
use Illuminate\Database\Seeder;

class VisitScenariosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $staffUser = Staff::where('email', 'staff@gerayehealthcare.com')->first();
        $otherStaff = Staff::where('email', '!=', 'staff@gerayehealthcare.com')->first();
        $patientsOfStaffUser = Patient::where('registered_by_staff_id', $staffUser->id)->get();
        $otherPatients = Patient::where('registered_by_staff_id', '!=', $staffUser->id)->get();

        // Create a completed visit for a patient assigned to the staff user
        if ($staffUser && $patientsOfStaffUser->isNotEmpty()) {
            VisitService::factory()->create([
                'patient_id' => $patientsOfStaffUser->first()->id,
                'staff_id' => $staffUser->id,
                'status' => 'Completed',
            ]);
        }

        // Create a pending visit for another patient assigned to the staff user
        if ($staffUser && $patientsOfStaffUser->count() > 1) {
            VisitService::factory()->create([
                'patient_id' => $patientsOfStaffUser->last()->id,
                'staff_id' => $staffUser->id,
                'status' => 'Pending',
            ]);
        }

        // Create a completed visit for a patient not assigned to the staff user
        if ($otherStaff && $otherPatients->isNotEmpty()) {
            VisitService::factory()->create([
                'patient_id' => $otherPatients->first()->id,
                'staff_id' => $otherStaff->id,
                'status' => 'Completed',
            ]);
        }
    }
}