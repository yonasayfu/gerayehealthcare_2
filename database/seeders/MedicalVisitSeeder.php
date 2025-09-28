<?php

namespace Database\Seeders;

use App\Models\MedicalVisit;
use App\Models\Patient;
use App\Models\Staff;
use Illuminate\Database\Seeder;

class MedicalVisitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get some patients and staff members
        $patients = Patient::all();
        $staff = Staff::all();

        if ($patients->isEmpty() || $staff->isEmpty()) {
            echo "Warning: Not enough patients or staff to create medical visits.\n";
            return;
        }

        // Create medical visits for patients
        foreach ($patients as $patient) {
            // Create 1-3 visits per patient
            $visitCount = rand(1, 3);

            for ($i = 0; $i < $visitCount; $i++) {
                MedicalVisit::create([
                    'patient_id' => $patient->id,
                    'visit_date' => now()->subDays(rand(0, 30))->subHours(rand(0, 23)),
                    'visit_type' => $this->getRandomVisitType(),
                    'doctor_id' => $staff->random()->id,
                    'status' => $this->getRandomStatus(),
                    'notes' => $this->getRandomNotes(),
                ]);
            }
        }

        echo "Medical visits seeded successfully.\n";
    }

    private function getRandomVisitType(): string
    {
        $types = ['initial', 'follow_up', 'emergency', 'routine_checkup', 'consultation'];
        return $types[array_rand($types)];
    }

    private function getRandomStatus(): string
    {
        $statuses = ['scheduled', 'completed', 'cancelled', 'in_progress'];
        return $statuses[array_rand($statuses)];
    }

    private function getRandomNotes(): ?string
    {
        $notes = [
            'Patient presented with mild symptoms.',
            'Follow-up required in 2 weeks.',
            'Prescription provided.',
            'Lab tests ordered.',
            'Patient advised on lifestyle changes.',
            null, // Sometimes no notes
        ];
        return $notes[array_rand($notes)];
    }
}
