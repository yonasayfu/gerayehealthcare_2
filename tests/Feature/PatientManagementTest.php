<?php

namespace Tests\Feature;

use App\Enums\RoleEnum;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PatientManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Create the Admin role needed for the tests
        Role::create(['name' => RoleEnum::ADMIN->value]);
    }

    #[Test]
    public function it_does_not_crash_when_sort_parameter_is_empty(): void
    {
        // Arrange: Create and log in as an Admin user
        $adminUser = User::factory()->create();
        $adminUser->assignRole(RoleEnum::ADMIN->value);

        // Act: Call the index route with an empty 'sort' parameter, which causes the bug
        $response = $this->actingAs($adminUser)->get(route('admin.patients.index', ['sort' => '']));

        // Assert: Check that the request was successful and did not crash
        $response->assertOk();
    }
}
