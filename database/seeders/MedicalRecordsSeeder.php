<?php

namespace Database\Seeders;

use App\Models\MedicalDocument;
use App\Models\MedicalVisit;
use App\Models\Patient;
use App\Models\Prescription;
use App\Models\PrescriptionItem;
use App\Models\Staff;
use Illuminate\Database\Seeder;

class MedicalRecordsSeeder extends Seeder
{
    public function run(): void
    {
        // Ensure we have some patients and staff
        if (Patient::count() === 0) {
            \App\Models\Patient::factory(10)->create();
        }
        if (Staff::count() === 0) {
            \App\Models\Staff::factory(5)->create();
        }

        // Create visits
        $visits = MedicalVisit::factory(25)->create();

        // Create documents linked randomly to visits/patients
        $documents = MedicalDocument::factory(40)->create();

        // Create prescriptions, some linked to documents
        $prescriptions = Prescription::factory(30)->create();

        // For each prescription, add 2-4 items
        $prescriptions->each(function (Prescription $prescription) {
            PrescriptionItem::factory(rand(2, 4))->create([
                'prescription_id' => $prescription->id,
            ]);
        });

        // Also ensure a few prescriptions link to existing documents of type 'prescription'
        $prescriptionDocs = $documents->where('document_type', 'prescription')->take(10);
        foreach ($prescriptionDocs as $doc) {
            Prescription::factory()->create([
                'patient_id' => $doc->patient_id,
                'medical_document_id' => $doc->id,
                'prescribed_date' => $doc->document_date,
            ]);
        }
    }
}
