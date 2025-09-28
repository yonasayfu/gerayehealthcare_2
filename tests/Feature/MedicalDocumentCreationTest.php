<?php

namespace Tests\Feature;

use App\Models\MedicalDocument;
use App\Models\Patient;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class MedicalDocumentCreationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Create a user and a staff member associated with that user for general authentication
        $user = User::factory()->create();
        $staff = Staff::factory()->create(['user_id' => $user->id]);
        // Assign necessary permissions for creating medical documents
        $user->givePermissionTo(['view medical documents', 'create medical documents']);
        $this->actingAs($user);
    }

    public function test_can_render_the_create_medical_document_page_for_a_staff_member(): void
    {
        // The setUp method already creates a staff user and authenticates them.
        // So, we just use the authenticated user.
        $response = $this->get(route('admin.medical-documents.create'));

        $response->assertOk(); // Expect a 200 OK response
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Admin/MedicalDocuments/Create')
            ->has('patients')
        );
    }

    public function test_cannot_render_the_create_medical_document_page_for_a_non_staff_member(): void
    {
        $user = User::factory()->create(); // Create a user without a staff record
        $this->actingAs($user);

        $response = $this->get(route('admin.medical-documents.create'));

        $response->assertForbidden();
    }

    public function test_can_create_a_medical_document_as_a_staff_member(): void
    {
        $user = User::factory()->create();
        $staff = Staff::factory()->for($user)->create();
        $this->actingAs($user);

        $patient = Patient::factory()->create();

        $documentData = MedicalDocument::factory()->make([
            'patient_id' => $patient->id,
            'created_by_staff_id' => $staff->id,
        ])->toArray();

        $response = $this->post(route('admin.medical-documents.store'), $documentData);

        $response->assertRedirect(route('admin.medical-documents.index'));
        $this->assertDatabaseHas('medical_documents', [
            'patient_id' => $patient->id,
            'created_by_staff_id' => $staff->id,
            'title' => $documentData['title'],
        ]);
    }

    public function test_cannot_create_a_medical_document_without_created_by_staff_id(): void
    {
        $user = User::factory()->create();
        Staff::factory()->for($user)->create();
        $this->actingAs($user);

        $patient = Patient::factory()->create();

        $documentData = MedicalDocument::factory()->make([
            'patient_id' => $patient->id,
            'created_by_staff_id' => null, // Explicitly set to null
        ])->toArray();

        $response = $this->post(route('admin.medical-documents.store'), $documentData);

        $response->assertSessionHasErrors('created_by_staff_id');
        $this->assertDatabaseMissing('medical_documents', [
            'patient_id' => $patient->id,
            'title' => $documentData['title'],
        ]);
    }

    public function test_cannot_create_a_medical_document_with_an_invalid_created_by_staff_id(): void
    {
        $user = User::factory()->create();
        Staff::factory()->for($user)->create();
        $this->actingAs($user);

        $patient = Patient::factory()->create();

        $documentData = MedicalDocument::factory()->make([
            'patient_id' => $patient->id,
            'created_by_staff_id' => 99999, // An ID that does not exist
        ])->toArray();

        $response = $this->post(route('admin.medical-documents.store'), $documentData);

        $response->assertSessionHasErrors('created_by_staff_id');
        $this->assertDatabaseMissing('medical_documents', [
            'patient_id' => $patient->id,
            'title' => $documentData['title'],
        ]);
    }
}
