<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Patient;
use App\Models\CorporateClient;
use App\Models\InsuranceCompany;
use App\Models\InsurancePolicy;
use App\Models\EmployeeInsuranceRecord;
use App\Models\Service;
use App\Models\Staff;
use App\Models\VisitService;
use App\Models\Invoice;
use App\Models\InsuranceClaim;

class MrXInsuranceScenarioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Step 1: Create core entities
        $nyalaInsurance = InsuranceCompany::firstOrCreate(
            ['name' => 'Nyala Insurance Company'],
            ['name_amharic' => 'ናይላ ኢንሹራንስ', 'contact_person' => 'Abebe Kebede', 'contact_email' => 'info@nyalainsurance.com', 'contact_phone' => '+251911223344', 'address' => 'Addis Ababa', 'address_amharic' => 'አዲስ አበባ']
        );

        $aslmNgo = CorporateClient::firstOrCreate(
            ['organization_name' => 'ASLM NGO'],
            ['organization_name_amharic' => 'ኤ.ኤስ.ኤል.ኤም. የሲቪል ማህበራት ድርጅት', 'contact_person' => 'Tigist Worku', 'contact_email' => 'info@aslmngo.org', 'contact_phone' => '+251944556677', 'tin_number' => '0012345678', 'trade_license_number' => 'TL/001/2015', 'address' => 'Addis Ababa']
        );

        $physiotherapyService = Service::firstOrCreate(
            ['name' => 'Physiotherapy'],
            ['description' => 'Physical therapy services', 'price' => 1000.00, 'duration' => 60, 'is_active' => true]
        );

        $nyalaAslmPolicy = InsurancePolicy::firstOrCreate(
            [
                'insurance_company_id' => $nyalaInsurance->id,
                'corporate_client_id' => $aslmNgo->id,
                'service_type' => 'Physiotherapy',
            ],
            [
                'service_type_amharic' => 'ፊዚዮቴራፒ',
                'coverage_percentage' => 80.00,
                'coverage_type' => 'Partial',
                'is_active' => true,
                'notes' => '80% coverage for physiotherapy services for ASLM NGO employees.',
            ]
        );

        // Step 2: Mr. X Registration
        $mrX = Patient::firstOrCreate(
            ['full_name' => 'Mr. X'],
            [
                'fayda_id' => 'FAYDA-MRX-001',
                'date_of_birth' => '1980-01-15',
                'gender' => 'Male',
                'address' => 'Addis Ababa, Ethiopia',
                'phone_number' => '+251912345678',
                'email' => 'mr.x@example.com',
                'emergency_contact' => 'Mrs. X - +251987654321',
                'source' => 'Referral',
                'geolocation' => '9.0000,38.0000',
                'registered_by_staff_id' => Staff::inRandomOrder()->first()->id ?? Staff::factory()->create()->id,
            ]
        );

        EmployeeInsuranceRecord::firstOrCreate(
            [
                'patient_id' => $mrX->id,
                'policy_id' => $nyalaAslmPolicy->id,
            ],
            [
                'kebele_id' => 'KB-001',
                'woreda' => 'Woreda 1',
                'region' => 'Addis Ababa',
                'federal_id' => 'FED-MRX-001',
                'ministry_department' => 'ASLM NGO',
                'employee_id_number' => 'EMP-ASLM-001',
                'verified' => true,
                'verified_at' => now(),
            ]
        );

        // Step 3: Service Scheduling and Delivery
        $caregiverUser = \App\Models\User::firstOrCreate(
            ['email' => 'caregiver.y@example.com'],
            [
                'name' => 'Caregiver Y',
                'password' => bcrypt('password'),
            ]
        );

        $caregiver = Staff::firstOrCreate(
            ['first_name' => 'Caregiver', 'last_name' => 'Y'],
            ['email' => 'caregiver.y@example.com', 'phone' => '+251911111111', 'position' => 'Caregiver', 'department' => 'Nursing', 'role' => 'Caregiver', 'status' => 'Active', 'hire_date' => now(), 'user_id' => $caregiverUser->id]
        );

        $visitService = VisitService::firstOrCreate(
            [
                'patient_id' => $mrX->id,
                'staff_id' => $caregiver->id,
                'scheduled_at' => now()->addDays(1),
            ],
            [
                'check_in_time' => now()->addDays(1)->addHours(9),
                'check_out_time' => now()->addDays(1)->addHours(10),
                'visit_notes' => 'Physiotherapy session completed successfully.',
                'status' => 'Completed',
            ]
        );

        // Step 4: Invoice Generation
        $totalAmount = $physiotherapyService->price; // 1000 ETB
        $insuranceCoverage = $nyalaAslmPolicy->coverage_percentage / 100;
        $insurancePortion = $totalAmount * $insuranceCoverage; // 800 ETB
        $employerCopay = $totalAmount - $insurancePortion; // 200 ETB

        $invoice = Invoice::firstOrCreate(
            [
                'patient_id' => $mrX->id,
                'service_id' => $visitService->id, // Link to visit service
                'invoice_date' => now()->toDateString(),
            ],
            [
                'due_date' => now()->addDays(7)->toDateString(),
                'subtotal' => $totalAmount,
                'tax_amount' => 0.00,
                'grand_total' => $totalAmount,
                'amount' => $totalAmount,
                'status' => 'Pending',
            ]
        );

        // Step 5: Insurance Claim Creation
        $insuranceClaim = InsuranceClaim::firstOrCreate(
            [
                'patient_id' => $mrX->id,
                'invoice_id' => $invoice->id,
                'policy_id' => $nyalaAslmPolicy->id,
            ],
            [
                'insurance_company_id' => $nyalaInsurance->id,
                'claim_status' => 'Submitted',
                'coverage_amount' => $insurancePortion,
                'paid_amount' => 0.00,
                'submitted_at' => now(),
                'reimbursement_required' => true,
                'is_pre_authorized' => false,
            ]
        );

        // Step 6: Simulate Payment Processing (Optional, can be done manually later)
        // To simulate payment, you would update the claim and invoice:
        // $insuranceClaim->update([
        //     'paid_amount' => $insurancePortion,
        //     'claim_status' => 'Paid',
        //     'payment_received_at' => now(),
        // ]);
        // $invoice->update([
        //     'status' => 'Paid',
        //     'paid_at' => now(),
        // ]);

        $this->command->info('Mr. X Insurance Scenario seeded successfully!');
    }
}
