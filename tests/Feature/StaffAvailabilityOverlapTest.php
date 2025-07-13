<?php

namespace Tests\Feature;

use App\Enums\RoleEnum;
use App\Models\Staff;
use App\Models\StaffAvailability;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class StaffAvailabilityOverlapTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['app.timezone' => 'Africa/Addis_Ababa']);
        Role::create(['name' => RoleEnum::STAFF->value]);
        Role::create(['name' => RoleEnum::ADMIN->value]);
    }

    #[Test]
    public function admin_cannot_create_overlapping_availability_slots(): void
    {
        // Create admin user
        $adminUser = User::factory()->create();
        $adminUser->assignRole(RoleEnum::ADMIN->value);

        // Create staff
        $staff = Staff::factory()->create();

        // Create initial availability slot
        StaffAvailability::create([
            'staff_id' => $staff->id,
            'start_time' => '2025-08-15 09:00:00',
            'end_time' => '2025-08-15 10:00:00',
            'status' => 'Available',
        ]);

        // Try to create overlapping slot
        $response = $this->actingAs($adminUser)->post(route('admin.staff-availabilities.store'), [
            'staff_id' => $staff->id,
            'start_time' => '2025-08-15 09:30:00',
            'end_time' => '2025-08-15 10:30:00',
            'status' => 'Unavailable',
        ]);

        // Should redirect back with error
        $response->assertRedirect();
        $response->assertSessionHasErrors(['error']);

        // Should not create the overlapping slot
        $this->assertEquals(1, StaffAvailability::where('staff_id', $staff->id)->count());
    }

    #[Test]
    public function staff_cannot_create_overlapping_availability_slots(): void
    {
        // Create staff user
        $staffUser = User::factory()->create();
        $staffUser->assignRole(RoleEnum::STAFF->value);
        $staff = Staff::factory()->create(['user_id' => $staffUser->id]);

        // Create initial availability slot
        StaffAvailability::create([
            'staff_id' => $staff->id,
            'start_time' => '2025-08-15 09:00:00',
            'end_time' => '2025-08-15 10:00:00',
            'status' => 'Available',
        ]);

        // Try to create overlapping slot
        $response = $this->actingAs($staffUser)->post(route('staff.my-availability.store'), [
            'start_time' => '2025-08-15 09:30:00',
            'end_time' => '2025-08-15 10:30:00',
            'status' => 'Unavailable',
        ]);

        // Should redirect back with error
        $response->assertRedirect();
        $response->assertSessionHasErrors(['error']);

        // Should not create the overlapping slot
        $this->assertEquals(1, StaffAvailability::where('staff_id', $staff->id)->count());
    }

    #[Test]
    public function can_create_non_overlapping_availability_slots(): void
    {
        // Create staff user
        $staffUser = User::factory()->create();
        $staffUser->assignRole(RoleEnum::STAFF->value);
        $staff = Staff::factory()->create(['user_id' => $staffUser->id]);

        // Create initial availability slot
        StaffAvailability::create([
            'staff_id' => $staff->id,
            'start_time' => '2025-08-15 09:00:00',
            'end_time' => '2025-08-15 10:00:00',
            'status' => 'Available',
        ]);

        // Create non-overlapping slot
        $response = $this->actingAs($staffUser)->post(route('staff.my-availability.store'), [
            'start_time' => '2025-08-15 10:00:00',
            'end_time' => '2025-08-15 11:00:00',
            'status' => 'Available',
        ]);

        // Should succeed
        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Should create the non-overlapping slot
        $this->assertEquals(2, StaffAvailability::where('staff_id', $staff->id)->count());
    }
}
