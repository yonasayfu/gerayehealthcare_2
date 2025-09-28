<?php

namespace Database\Seeders;

use App\Models\Patient;
use App\Models\Staff;
use Illuminate\Database\Seeder;

class PatientScenariosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $staffUser = Staff::where('email', 'staff@gerayehealthcare.com')->first();

        // Create a few patients and assign them to the staff user
        if ($staffUser) {
            Patient::factory()->count(2)->create([
                'registered_by_staff_id' => $staffUser->id,
            ]);
        }

        // Create a patient with insurance
        Patient::factory()->withInsurance()->create();

        // Create a patient registered by the police
        Patient::factory()->registeredByPolice()->create();

        // Create a couple of patients not assigned to any specific staff
        Patient::factory()->count(2)->create();
    }
}
