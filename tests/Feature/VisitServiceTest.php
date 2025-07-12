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
        // ARRANGE
        Role::create(['name' => RoleEnum::ADMIN->value]);
        $admin = User::factory()->create()->assignRole(RoleEnum::ADMIN->value);
        $patient = Patient::factory()->create();
        $staff = Staff::factory()->create(['role' => 'Nurse']);

        // Create the assignment directly to bypass any factory issues
        $assignment = CaregiverAssignment::create([
            'patient_id' => $patient->id,
            'staff_id' => $staff->id,
            'status' => 'Assigned', // Guarantee the status is 'Assigned'
            'shift_start' => now(),
            'shift_end' => now()->addHours(8),
        ]);

        // ACT
        $this->actingAs($admin)->post(route('admin.visit-services.store'), [
            'patient_id' => $patient->id,
            'staff_id' => $staff->id,
            'scheduled_at' => now()->addDay()->toDateTimeString(),
            'status' => 'Pending',
        ]);

        // ASSERT
        $this->assertDatabaseHas('visit_services', [
            'patient_id' => $patient->id,
            'assignment_id' => $assignment->id,
        ]);
    }
}