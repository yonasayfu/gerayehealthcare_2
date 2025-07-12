<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\CaregiverAssignment;
use App\Models\Patient;
use App\Models\Staff;
use App\Models\User;
use App\Enums\RoleEnum;

class VisitServiceTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_saves_the_correct_assignment_id_when_creating_a_visit()
    {
        // Arrange: Create an Admin user to perform the action
        $admin = User::factory()->create();
        $admin->assignRole(RoleEnum::ADMIN->value);

        // Arrange: Create our sample data
        $patient = Patient::factory()->create();
        $staff = Staff::factory()->create();

        // Arrange: Create the long-term assignment linking the patient and staff
        $assignment = CaregiverAssignment::factory()->create([
            'patient_id' => $patient->id,
            'staff_id' => $staff->id,
        ]);

        // Act: As the admin, try to create a new Visit Service for this patient.
        // We are NOT passing assignment_id in the post data, the controller should find it.
        $this->actingAs($admin)->post(route('admin.visit-services.store'), [
            'patient_id' => $patient->id,
            'staff_id' => $staff->id,
            'scheduled_at' => now()->addDay(),
            'status' => 'Pending',
            // visit_notes, etc. can be added if required by validation
        ]);

        // Assert: Check that the new record in the 'visit_services' table
        // has been correctly stamped with the ID of the master assignment.
        $this->assertDatabaseHas('visit_services', [
            'patient_id' => $patient->id,
            'assignment_id' => $assignment->id,
        ]);
    }
}