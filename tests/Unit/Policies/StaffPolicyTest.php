<?php

namespace Tests\Unit\Policies;

use App\Enums\RoleEnum;
use App\Models\Staff;
use App\Models\User;
use App\Policies\StaffPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StaffPolicyTest extends TestCase
{
    use RefreshDatabase;

    protected StaffPolicy $policy;
    protected User $adminUser;
    protected User $managerUser;
    protected User $hrUser;
    protected User $regularUser;
    protected Staff $staffMember;
    protected Staff $ownStaffRecord;

    protected function setUp(): void
    {
        parent::setUp();

        $this->policy = new StaffPolicy();

        // Create users with different roles
        $this->adminUser = User::factory()->create();
        $this->adminUser->assignRole(RoleEnum::ADMIN->value);

        $this->managerUser = User::factory()->create();
        $this->managerUser->assignRole(RoleEnum::MANAGER->value);

        $this->hrUser = User::factory()->create();
        $this->hrUser->assignRole(RoleEnum::HR->value);

        $this->regularUser = User::factory()->create();

        // Create staff members
        $this->staffMember = Staff::factory()->create();
        $this->ownStaffRecord = Staff::factory()->create(['user_id' => $this->regularUser->id]);
    }

    public function test_view_any_policy()
    {
        // Admin, manager, and HR users can view all staff
        $this->assertTrue($this->policy->viewAny($this->adminUser));
        $this->assertTrue($this->policy->viewAny($this->managerUser));
        $this->assertTrue($this->policy->viewAny($this->hrUser));

        // Regular users cannot view all staff
        $this->assertFalse($this->policy->viewAny($this->regularUser));
    }

    public function test_view_policy()
    {
        // Admin, manager, and HR users can view any staff record
        $this->assertTrue($this->policy->view($this->adminUser, $this->staffMember));
        $this->assertTrue($this->policy->view($this->managerUser, $this->staffMember));
        $this->assertTrue($this->policy->view($this->hrUser, $this->staffMember));

        // Regular users can only view their own staff record
        $this->assertFalse($this->policy->view($this->regularUser, $this->staffMember));
        $this->assertTrue($this->policy->view($this->regularUser, $this->ownStaffRecord));
    }

    public function test_create_policy()
    {
        // Admin, manager, and HR users can create staff records
        $this->assertTrue($this->policy->create($this->adminUser));
        $this->assertTrue($this->policy->create($this->managerUser));
        $this->assertTrue($this->policy->create($this->hrUser));

        // Regular users cannot create staff records
        $this->assertFalse($this->policy->create($this->regularUser));
    }

    public function test_update_policy()
    {
        // Admin, manager, and HR users can update any staff record
        $this->assertTrue($this->policy->update($this->adminUser, $this->staffMember));
        $this->assertTrue($this->policy->update($this->managerUser, $this->staffMember));
        $this->assertTrue($this->policy->update($this->hrUser, $this->staffMember));

        // Regular users can only update their own staff record
        $this->assertFalse($this->policy->update($this->regularUser, $this->staffMember));
        $this->assertTrue($this->policy->update($this->regularUser, $this->ownStaffRecord));
    }

    public function test_delete_policy()
    {
        // Only admin and HR users can delete staff records
        $this->assertTrue($this->policy->delete($this->adminUser, $this->staffMember));
        $this->assertTrue($this->policy->delete($this->hrUser, $this->staffMember));

        // Managers and regular users cannot delete staff records
        $this->assertFalse($this->policy->delete($this->managerUser, $this->staffMember));
        $this->assertFalse($this->policy->delete($this->regularUser, $this->staffMember));
    }
}
