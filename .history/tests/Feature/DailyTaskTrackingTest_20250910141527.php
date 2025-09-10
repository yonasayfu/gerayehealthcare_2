<?php

namespace Tests\Feature;

use App\Models\PersonalTask;
use App\Models\TaskDelegation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DailyTaskTrackingTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $staff;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a test user with staff profile
        $this->user = User::factory()->create();
        $this->staff = \App\Models\Staff::factory()->create([
            'user_id' => $this->user->id
        ]);
    }

    /** @test */
    public function user_can_view_daily_task_tracking_dashboard()
    {
        $response = $this->actingAs($this->user)
            ->get(route('staff.daily-tasks.index'));

        $response->assertStatus(200);
        $response->assertInertiaHas('personalTasks');
        $response->assertInertiaHas('delegatedTasks');
    }

    /** @test */
    public function user_can_update_personal_task_with_tracking_info()
    {
        $task = PersonalTask::factory()->create([
            'user_id' => $this->user->id
        ]);

        $response = $this->actingAs($this->user)
            ->post(route('staff.daily-tasks.update'), [
                'task_type' => 'personal',
                'task_id' => $task->id,
                'task_date' => '2025-09-10',
                'start_time' => '09:00',
                'end_time' => '10:30',
                'estimated_duration_minutes' => 90,
                'daily_notes' => 'Worked on project requirements',
                'task_category' => 'Development',
                'priority_level' => 4,
                'is_billable' => true,
            ]);

        $response->assertStatus(200);
        
        $this->assertDatabaseHas('personal_tasks', [
            'id' => $task->id,
            'task_date' => '2025-09-10',
            'start_time' => '09:00:00',
            'end_time' => '10:30:00',
            'estimated_duration_minutes' => 90,
            'daily_notes' => 'Worked on project requirements',
            'task_category' => 'Development',
            'priority_level' => 4,
            'is_billable' => true,
        ]);
    }

    /** @test */
    public function user_can_update_delegated_task_with_tracking_info()
    {
        $task = TaskDelegation::factory()->create([
            'assigned_to' => $this->staff->id
        ]);

        $response = $this->actingAs($this->user)
            ->post(route('staff.daily-tasks.update'), [
                'task_type' => 'delegated',
                'task_id' => $task->id,
                'task_date' => '2025-09-10',
                'start_time' => '09:00',
                'end_time' => '10:30',
                'estimated_duration_minutes' => 90,
                'daily_notes' => 'Completed initial analysis',
                'task_category' => 'Analysis',
                'priority_level' => 3,
                'is_billable' => false,
                'progress_status' => 'completed',
            ]);

        $response->assertStatus(200);
        
        $this->assertDatabaseHas('task_delegations', [
            'id' => $task->id,
            'task_date' => '2025-09-10',
            'start_time' => '09:00:00',
            'end_time' => '10:30:00',
            'estimated_duration_minutes' => 90,
            'daily_notes' => 'Completed initial analysis',
            'task_category' => 'Analysis',
            'priority_level' => 3,
            'is_billable' => false,
            'progress_status' => 'completed',
        ]);
    }

    /** @test */
    public function supervisor_can_view_kpi_dashboard()
    {
        // Make user a supervisor
        $this->user->assignRole('Super Admin');

        $response = $this->actingAs($this->user)
            ->get(route('staff.kpi-dashboard'));

        $response->assertStatus(200);
        $response->assertInertiaHas('kpiData');
    }

    /** @test */
    public function non_supervisor_cannot_view_kpi_dashboard()
    {
        // Remove any roles
        $this->user->syncRoles([]);

        $response = $this->actingAs($this->user)
            ->get(route('staff.kpi-dashboard'));

        $response->assertStatus(302); // Redirected
    }
}