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

class AdminCalendarTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Set the app timezone for the test
        config(['app.timezone' => 'Africa/Addis_Ababa']);
        // Create the necessary roles
        Role::create(['name' => RoleEnum::ADMIN->value]);
    }

    #[Test]
    public function admin_get_events_returns_correctly_formatted_local_time(): void
    {
        // 1. ARRANGE
        // Create an Admin user to perform the action
        $adminUser = User::factory()->create();
        $adminUser->assignRole(RoleEnum::ADMIN->value);

        // Create a separate Staff member to assign the availability to
        $staff = Staff::factory()->create();

        // Save a time as 10:00 AM local time in the database
        $localTime = '2025-08-15 10:00:00';
        StaffAvailability::create([
            'staff_id' => $staff->id,
            'start_time' => $localTime,
            'end_time' => '2025-08-15 11:00:00',
            'status' => 'Available',
        ]);
        
        // Expect the time to be the same as what we stored (local time)
        $expectedLocalTime = '2025-08-15 10:00:00';

        // 2. ACT
        // Call the admin API endpoint as the authenticated Admin user
        $response = $this->actingAs($adminUser)->getJson(route('admin.staff-availabilities.events', [
            'start' => '2025-08-01',
            'end' => '2025-08-30',
        ]));

        // 3. ASSERT
        // Check that the request was successful and the JSON contains the correct local time
        $response->assertOk();
        $response->assertJsonFragment([
            'start' => $expectedLocalTime,
        ]);
    }
}