<?php

namespace Tests\Feature;

// Import the new Attribute class and the Role model
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;

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

    #[Test] // This is the new attribute style that fixes the WARN
    public function it_saves_the_correct_assignment_id_when_creating_a_visit(): void
    {
        // Arrange: Create the 'Admin' role in the test database first
        Role::create(['name' => RoleEnum::ADMIN->value]);

        // Now, create the Admin user and assign the role
        $admin = User::factory()->create();
        $admin->assignRole(RoleEnum::ADMIN->value);

        // Arrange: Create our sample data
        $patient = Patient::factory()->create();
        $staff = Staff::factory()->create();
        $assignment = CaregiverAssignment::factory()->create([
            'patient_id' => $patient->id,
            'staff_id' => $staff->id,
        ]);

        // Act: As the admin, try to create a new Visit Service
        $this->actingAs($admin)->post(route('admin.visit-services.store'), [
            'patient_id' => $patient->id,
            'staff_id' => $staff->id,
            'scheduled_at' => now()->addDay(),
            'status' => 'Pending',
        ]);

        // Assert: Check that the new record has the correct assignment_id
        $this->assertDatabaseHas('visit_services', [
            'patient_id' => $patient->id,
            'assignment_id' => $assignment->id,
        ]);
    }
}