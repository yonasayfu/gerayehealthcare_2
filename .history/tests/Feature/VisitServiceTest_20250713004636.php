<?php

namespace Tests\Feature;

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

    #[Test]
    public function it_saves_the_correct_assignment_id_when_creating_a_visit(): void
    {
        // --- ARRANGE ---
        Role::create(['name' => RoleEnum::ADMIN->value]);
        $admin = User::factory()->create()->assignRole(RoleEnum::ADMIN->value);
        $patient = Patient::factory()->create();
        $staff = Staff::factory()->create();
        $assignment = CaregiverAssignment::factory()->create([
            'patient_id' => $patient->id,
            'staff_id' => $staff->id,
        ]);

        // --- NEW CHECKPOINT ---
        // Let's assert that the assignment we just created definitely exists
        // in the database before we even call the controller.
        $this->assertDatabaseHas('caregiver_assignments', [
            'id' => $assignment->id,
            'patient_id' => $patient->id,
            'staff_id' => $staff->id,
        ]);

        // --- ACT ---
        $this->actingAs($admin)->post(route('admin.visit-services.store'), [
            'patient_id' => $patient->id,
            'staff_id' => $staff->id,
            'scheduled_at' => now()->addDay(),
            'status' => 'Pending',
        ]);

        // --- ASSERT ---
        // This is the original assertion that was failing.
        $this->assertDatabaseHas('visit_services', [
            'patient_id' => $patient->id,
            'assignment_id' => $assignment->id,
        ]);
    }
}