<?php

namespace Tests\Feature;

use App\Enums\RoleEnum;
use App\Models\Patient;
use App\Models\Staff;
use App\Models\StaffAvailability;
use App\Models\User;
use App\Models\VisitService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use App\Rules\StaffIsAvailableForVisit;
use Spatie\Permission\Models\Role; // <-- THIS IS THE MISSING LINE

class StaffAvailabilityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Create the Admin role needed for the tests
        Role::create(['name' => RoleEnum::ADMIN->value]);
    }

    #[Test]
    public function it_prevents_scheduling_a_visit_during_an_unavailable_slot(): void
    {
        // 1. ARRANGE
        $staff = Staff::factory()->create();
        StaffAvailability::create([
            'staff_id' => $staff->id,
            'start_time' => '2025-08-15 10:00:00',
            'end_time' => '2025-08-11 12:00:00',
            'status' => 'Unavailable',
        ]);

        // 2. ACT
        $validator = Validator::make(
            ['staff_id' => $staff->id, 'scheduled_at' => '2025-08-15 10:30:00'],
            ['staff_id' => ['required', new StaffIsAvailableForVisit]]
        );

        // 3. ASSERT
        $this->assertFalse($validator->passes());
    }

    #[Test]
    public function it_prevents_scheduling_a_visit_that_conflicts_with_another_visit(): void
    {
        // 1. ARRANGE
        $staff = Staff::factory()->create();
        $patient = Patient::factory()->create();
        VisitService::create([
            'staff_id' => $staff->id,
            'patient_id' => $patient->id,
            'scheduled_at' => '2025-08-15 14:00:00',
            'status' => 'Pending',
        ]);

        // 2. ACT
        $validator = Validator::make(
            ['staff_id' => $staff->id, 'scheduled_at' => '2025-08-15 14:30:00'],
            ['staff_id' => ['required', new StaffIsAvailableForVisit]]
        );

        // 3. ASSERT
        $this->assertFalse($validator->passes());
    }

    #[Test]
    public function it_allows_scheduling_a_visit_when_the_staff_is_free(): void
    {
        // 1. ARRANGE
        $staff = Staff::factory()->create();

        // 2. ACT
        $validator = Validator::make(
            ['staff_id' => $staff->id, 'scheduled_at' => '2025-08-15 16:00:00'],
            ['staff_id' => ['required', new StaffIsAvailableForVisit]]
        );

        // 3. ASSERT
        $this->assertTrue($validator->passes());
    }
}