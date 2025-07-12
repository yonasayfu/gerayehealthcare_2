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
        // THIS IS THE ONLY CHANGE: Force Laravel to show the real error
        $this->withoutExceptionHandling();

        // --- ARRANGE ---
        Role::create(['name' => RoleEnum::ADMIN->value]);
        $admin = User::factory()->create()->assignRole(RoleEnum::ADMIN->value);
        $patient = Patient::factory()->create();
        $staff = Staff::factory()->create();
        $assignment = CaregiverAssignment::factory()->create([
            'patient_id' => $patient->id,
            'staff_id' => $staff->id,
            'status' => 'Assigned', // Be explicit about the status
        ]);

        // --- ACT ---
        $this->actingAs($admin)->post(route('admin.visit-services.store'), [
            'patient_id' => $patient->id,
            'staff_id' => $staff->id,
            'scheduled_at' => now()->addDay()->toDateTimeString(),
            'status' => 'Pending',
        ]);

        // --- ASSERT ---
        $this->assertDatabaseHas('visit_services', [
            'patient_id' => $patient->id,
            'assignment_id' => $assignment->id,
        ]);
    }
}