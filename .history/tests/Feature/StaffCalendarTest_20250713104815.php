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

class StaffCalendarTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Set the app timezone for this test to match your configuration
        config(['app.timezone' => 'Africa/Addis_Ababa']);
        // Create the necessary roles
        Role::create(['name' => RoleEnum::STAFF->value]);
    }

    #[Test]
    public function get_events_returns_correctly_formatted_local_time(): void
    {
        // 1. ARRANGE
        // Create a staff user
        $staffUser = User::factory()->create();
        $staffUser->assignRole(RoleEnum::STAFF->value);
        $staff = Staff::factory()->create(['user_id' => $staffUser->id]);

        // Create an availability slot. We save it in the database as UTC time.
        // Let's use 10:00 AM UTC.
        $utcTime = '2025-08-15 10:00:00';
        StaffAvailability::create([
            'staff_id' => $staff->id,
            'start_time' => $utcTime,
            'end_time' => '2025-08-15 11:00:00',
            'status' => 'Available',
        ]);
        
        // Define the expected time in your local timezone (EAT is UTC+3)
        // 10:00 UTC should become 13:00 EAT (1 PM)
        $expectedLocalTime = '2025-08-15 13:00:00';

        // 2. ACT
        // Call the API endpoint as the authenticated staff user
        $response = $this->actingAs($staffUser)->getJson(route('staff.my-availability.events', [
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