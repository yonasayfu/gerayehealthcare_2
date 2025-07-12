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
        
        CaregiverAssignment::factory()->create([
            'patient_id' => $patient->id,
            'staff_id' => $staff->id,
            'status' => 'Assigned', // We are telling the factory to use this status
        ]);

        // --- DUMP AND DIE ---
        // This will stop the test here and show us exactly what is in
        // the caregiver_assignments table right before the controller is called.
        // This is our ground truth.
        dd(CaregiverAssignment::all()->toArray());

        // The rest of the test will not run yet.
        $this->actingAs($admin)->post(route('admin.visit-services.store'), [
            'patient_id' => $patient->id,
            'staff_id' => $staff->id,
            'scheduled_at' => now()->addDay()->toDateTimeString(),
            'status' => 'Pending',
        ]);

        $this->assertDatabaseHas('visit_services', [
            'patient_id' => $patient->id,
            'assignment_id' => 1, // We expect assignment ID to be 1
        ]);
    }
}