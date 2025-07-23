<?php

namespace Database\Seeders;

use App\Models\Patient;
use App\Models\Service;
use App\Models\Staff;
use App\Models\VisitService;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TestInvoiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a few patients
        $patients = Patient::factory()->count(3)->create([
            'email' => fn () => fake()->unique()->safeEmail(), // Ensure patients have unique emails
        ]);

        // Get some staff and services to associate with visits
        $staff = Staff::all();
        $services = Service::all();

        if ($staff->isEmpty()) {
            // If no staff, create some
            Staff::factory()->count(5)->create();
            $staff = Staff::all();
        }

        if ($services->isEmpty()) {
            // If no services, create some
            Service::factory()->count(3)->create();
            $services = Service::all();
        }

        foreach ($patients as $patient) {
            // Create 2-5 completed, uninvoiced visits for each patient
            VisitService::factory()->count(rand(2, 5))->create([
                'patient_id' => $patient->id,
                'staff_id' => $staff->random()->id,
                'service_id' => $services->random()->id,
                'status' => 'Completed', // Mark as completed so they are billable
                'is_invoiced' => false, // Ensure they are not yet invoiced
            ]);
        }
    }
}
