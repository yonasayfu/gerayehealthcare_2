<?php

namespace Tests\Feature;

use App\Enums\RoleEnum;
use App\Models\Staff;
use App\Models\TaskDelegation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskDelegationTest extends TestCase
{
    use RefreshDatabase;

    protected $adminUser;
    protected $staffUser1;
    protected $staffUser2;
    protected $staff1;
    protected $staff2;

    protected function setUp(): void
    {
        parent::setUp();

        // Create admin user
        $this->adminUser = User::factory()->create();
        $this->adminUser->assignRole(RoleEnum::ADMIN->value);

        // Create staff users and their profiles
        $this->staffUser1 = User::factory()->create();
        $this->staff1 = Staff::factory()->create(['user_id' => $this->staffUser1->id]);
        $this->staffUser1->assignRole(RoleEnum::STAFF->value);

        $this->staffUser2 = User::factory()->create();
        $this->staff2 = Staff::factory()->create(['user_id' => $this->staffUser2->id]);
        $this->staffUser2->assignRole(RoleEnum::STAFF->value);
    }

    /** @test */
    public function staff_can_view_their_tasks()
    {
        // Create a task assigned to staff1
        $task = TaskDelegation::factory()->create([
            'assigned_to' => $this->staff1->id,
            'created_by' => $this->adminUser->id,
        ]);

        // Staff1 should be able to view their tasks
        $response = $this->actingAs($this->staffUser1)
            ->get(route('staff.task-delegations.index'));

        $response->assertStatus(200);
        $response->assertSee($task->title);
    }

    /** @test */
    public function staff_can_create_tasks()
    {
        $taskData = [
            'title' => 'Test Task',
            'due_date' => '2025-12-31',
            'assigned_to' => $this->staff2->id,
        ];

        $response = $this->actingAs($this->staffUser1)
            ->post(route('staff.task-delegations.store'), $taskData);

        $response->assertStatus(302);
        $this->assertDatabaseHas('task_delegations', [
            'title' => 'Test Task',
            'assigned_to' => $this->staff2->id,
            'created_by' => $this->staffUser1->id, // Should be set automatically
        ]);
    }

    /** @test */
    public function staff_can_transfer_tasks_they_are_assigned_to()
    {
        // Create a task assigned to staff1
        $task = TaskDelegation::factory()->create([
            'assigned_to' => $this->staff1->id,
            'created_by' => $this->adminUser->id,
        ]);

        // Staff1 transfers the task to staff2
        $response = $this->actingAs($this->staffUser1)
            ->patch(route('staff.task-delegations.update', $task), [
                'assigned_to' => $this->staff2->id,
            ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('task_delegations', [
            'id' => $task->id,
            'assigned_to' => $this->staff2->id,
        ]);
    }

    /** @test */
    public function staff_cannot_transfer_tasks_they_did_not_create_or_are_not_assigned_to()
    {
        // Create a task assigned to staff1
        $task = TaskDelegation::factory()->create([
            'assigned_to' => $this->staff1->id,
            'created_by' => $this->adminUser->id,
        ]);

        // Staff2 tries to transfer the task (should fail)
        $response = $this->actingAs($this->staffUser2)
            ->patch(route('staff.task-delegations.update', $task), [
                'assigned_to' => $this->staff2->id,
            ]);

        $response->assertStatus(403);
    }

    /** @test */
    public function admin_can_transfer_any_task()
    {
        // Create a task assigned to staff1
        $task = TaskDelegation::factory()->create([
            'assigned_to' => $this->staff1->id,
            'created_by' => $this->adminUser->id,
        ]);

        // Admin transfers the task to staff2
        $response = $this->actingAs($this->adminUser)
            ->patch(route('admin.task-delegations.update', $task), [
                'assigned_to' => $this->staff2->id,
            ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('task_delegations', [
            'id' => $task->id,
            'assigned_to' => $this->staff2->id,
        ]);
    }

    /** @test */
    public function tasks_include_creator_information()
    {
        // Create a task
        $task = TaskDelegation::factory()->create([
            'assigned_to' => $this->staff1->id,
            'created_by' => $this->adminUser->id,
        ]);

        // Admin views tasks
        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.task-delegations.index'));

        $response->assertStatus(200);
        $response->assertSee($this->adminUser->name); // Should show creator name
    }
}
