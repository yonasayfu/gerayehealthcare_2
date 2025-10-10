<?php

namespace Database\Seeders;

use App\Models\CaregiverAssignment;
use App\Models\MedicalDocument;
use App\Models\MedicalVisit;
use App\Models\Patient;
use App\Models\Prescription;
use App\Models\PrescriptionItem;
use App\Models\Service;
use App\Models\Staff;
use App\Models\VisitService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class ClinicalModuleSeeder extends Seeder
{
    /**
     * Seed clinical data: services, visits, documents, and prescriptions.
     */
    public function run(): void
    {
        $doctor = Staff::whereHas('user', fn ($query) => $query->where('email', 'doctor@gerayehealthcare.com'))->first();
        $nurse = Staff::whereHas('user', fn ($query) => $query->where('email', 'nurse@gerayehealthcare.com'))->first();
        $staffMembers = Staff::orderBy('id')->get();

        if ($staffMembers->isEmpty()) {
            $staffMembers = Staff::factory()->count(3)->create();
        }

        $serviceCatalog = [
            ['name' => 'Home Nursing Visit', 'category' => 'Nursing', 'price' => 450],
            ['name' => 'Chronic Care Check-in', 'category' => 'Clinical', 'price' => 520],
            ['name' => 'Post-Operative Follow-up', 'category' => 'Clinical', 'price' => 610],
            ['name' => 'Physiotherapy Session', 'category' => 'Therapy', 'price' => 380],
            ['name' => 'Health Coaching', 'category' => 'Wellness', 'price' => 210],
            ['name' => 'Telemedicine Review', 'category' => 'Consultation', 'price' => 300],
        ];

        foreach ($serviceCatalog as $service) {
            Service::updateOrCreate(
                ['name' => $service['name']],
                array_merge($service, [
                    'description' => $service['name'].' provided by the in-home care team.',
                    'duration' => 60,
                    'is_active' => true,
                ])
            );
        }

        if (Patient::count() < 6) {
            Patient::factory()->count(6 - Patient::count())->create();
        }

        $patients = Patient::orderBy('id')->take(6)->get();
        $services = Service::orderBy('id')->limit(6)->get();
        $start = Carbon::now()->startOfDay();

        foreach ($patients as $index => $patient) {
            $staff = $staffMembers[$index % $staffMembers->count()];
            CaregiverAssignment::updateOrCreate(
                [
                    'patient_id' => $patient->id,
                    'staff_id' => $staff->id,
                    'shift_start' => $start->copy()->addDays($index),
                ],
                [
                    'shift_end' => $start->copy()->addDays($index)->addHours(8),
                    'status' => 'Assigned',
                ]
            );
        }

        $scheduledVisits = collect();
        foreach ($patients->take(5) as $index => $patient) {
            $staff = $index % 2 === 0 ? ($doctor ?? $staffMembers[$index % $staffMembers->count()]) : ($nurse ?? $staffMembers[$index % $staffMembers->count()]);
            $service = $services[$index % $services->count()];

            $scheduledVisits->push(
                VisitService::factory()->create([
                    'patient_id' => $patient->id,
                    'staff_id' => $staff->id,
                    'service_id' => $service->id,
                    'scheduled_at' => Carbon::now()->addDays($index + 1),
                    'status' => $index % 2 === 0 ? 'Completed' : 'Pending',
                    'payment_status' => $index % 2 === 0 ? 'Paid' : 'Pending',
                ])
            );
        }

        $medicalVisits = collect();
        foreach ($patients->take(5) as $index => $patient) {
            $medicalVisits->push(
                MedicalVisit::factory()->create([
                    'patient_id' => $patient->id,
                    'doctor_id' => $doctor?->id ?? $staffMembers->first()->id,
                    'visit_type' => $index % 2 === 0 ? 'follow_up' : 'initial',
                    'status' => $index % 2 === 0 ? 'completed' : 'scheduled',
                ])
            );
        }

        foreach ($medicalVisits as $index => $visit) {
            MedicalDocument::factory()->create([
                'patient_id' => $visit->patient_id,
                'medical_visit_id' => $visit->id,
                'document_type' => $index % 2 === 0 ? 'doctor_note' : 'lab_result',
                'created_by_staff_id' => $doctor?->id ?? $staffMembers->first()->id,
            ]);
        }

        foreach ($medicalVisits as $visit) {
            $prescription = Prescription::factory()->create([
                'patient_id' => $visit->patient_id,
                'created_by_staff_id' => $doctor?->id ?? $staffMembers->first()->id,
                'status' => 'active',
            ]);

            PrescriptionItem::factory()->count(3)->create([
                'prescription_id' => $prescription->id,
            ]);
        }

        // Ensure every visit has at least one related document and prescription entry
        $scheduledVisits->each(function (VisitService $visit, int $index) use ($doctor, $staffMembers) {
            MedicalDocument::factory()->create([
                'patient_id' => $visit->patient_id,
                'medical_visit_id' => null,
                'document_type' => $index % 3 === 0 ? 'lab_request' : 'other',
                'created_by_staff_id' => $doctor?->id ?? $staffMembers->first()->id,
            ]);
        });
    }
}
