<?php

namespace Database\Seeders;

use App\Models\CorporateClient;
use App\Models\EmployeeInsuranceRecord;
use App\Models\InsuranceClaim;
use App\Models\InsuranceCompany;
use App\Models\InsurancePolicy;
use App\Models\Patient;
use Illuminate\Database\Seeder;

class InsuranceModuleSeeder extends Seeder
{
    /**
     * Seed insurance companies, policies, employee coverage, and claims.
     */
    public function run(): void
    {
        $companies = InsuranceCompany::factory()->count(5)->create();
        $corporateClients = CorporateClient::factory()->count(5)->create();

        $patients = Patient::orderBy('id')->take(5)->get();
        if ($patients->isEmpty()) {
            $patients = Patient::factory()->count(5)->create();
        }

        $policies = collect(range(0, 4))->map(function ($index) use ($companies, $corporateClients) {
            return InsurancePolicy::factory()->create([
                'insurance_company_id' => $companies[$index % $companies->count()]->id,
                'corporate_client_id' => $corporateClients[$index % $corporateClients->count()]->id,
                'coverage_percentage' => 70 + ($index * 5),
                'coverage_type' => $index % 2 === 0 ? 'Full' : 'Partial',
                'is_active' => true,
            ]);
        });

        foreach ($patients as $index => $patient) {
            $policy = $policies[$index % $policies->count()];
            EmployeeInsuranceRecord::factory()->create([
                'patient_id' => $patient->id,
                'policy_id' => $policy->id,
                'verified' => $index % 2 === 0,
            ]);

            InsuranceClaim::factory()->create([
                'patient_id' => $patient->id,
                'policy_id' => $policy->id,
                'claim_status' => $index % 2 === 0 ? 'Approved' : 'Pending',
            ]);
        }
    }
}
