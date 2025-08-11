<?php

namespace Tests\Feature;

use App\Models\Patient;
use App\Models\User;
use App\Models\CorporateClient;
use App\Models\InsurancePolicy;
use App\Models\EmployeeInsuranceRecord;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PatientTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutCsrfTokens();
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    /** @test */
    public function a_patient_can_be_registered_with_employer_and_policy_information()
    {
        $corporateClient = CorporateClient::factory()->create();
        $insurancePolicy = InsurancePolicy::factory()->create();

        $patientData = Patient::factory()->make([
            'corporate_client_id' => $corporateClient->id,
            'insurance_policy_id' => $insurancePolicy->id,
        ])->toArray();

        $response = $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class)->post(route('admin.patients.store'), $patientData);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('admin.patients.index'));

        $this->assertDatabaseHas('patients', [
            'first_name' => $patientData['first_name'],
            'last_name' => $patientData['last_name'],
            'corporate_client_id' => $corporateClient->id,
        ]);

        $patient = Patient::where('first_name', $patientData['first_name'])->first();

        $this->assertDatabaseHas('employee_insurance_records', [
            'patient_id' => $patient->id,
            'corporate_client_id' => $corporateClient->id,
            'insurance_policy_id' => $insurancePolicy->id,
        ]);
    }
}
