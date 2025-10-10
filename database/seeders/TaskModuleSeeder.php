<?php

namespace Database\Seeders;

use App\Models\PersonalTask;
use App\Models\PersonalTaskSubtask;
use App\Models\Staff;
use App\Models\TaskDelegation;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class TaskModuleSeeder extends Seeder
{
    /**
     * Seed task delegation, caregiver tasking, and personal productivity data.
     */
    public function run(): void
    {
        $staffMembers = Staff::orderBy('id')->take(6)->get();
        if ($staffMembers->isEmpty()) {
            $staffMembers = Staff::factory()->count(6)->create();
        }

        $users = User::orderBy('id')->take(6)->get();
        if ($users->isEmpty()) {
            $users = User::factory()->count(6)->create();
        }

        foreach ($staffMembers->take(5) as $index => $staff) {
            TaskDelegation::factory()->create([
                'assigned_to' => $staff->id,
                'due_date' => Carbon::now()->addDays($index + 2)->format('Y-m-d'),
                'status' => $index % 2 === 0 ? 'In Progress' : 'Pending',
            ]);
        }

        foreach ($users->take(5) as $index => $user) {
            $task = PersonalTask::factory()->create([
                'user_id' => $user->id,
                'due_date' => Carbon::now()->addDays($index + 1),
                'is_completed' => $index % 4 === 0,
                'task_category' => $index % 2 === 0 ? 'Clinical' : 'Operations',
                'priority_level' => $index % 5 + 1,
            ]);

            PersonalTaskSubtask::factory()->count(2)->create([
                'personal_task_id' => $task->id,
            ]);
        }

        // Add a handful of wrap-up tasks scheduled for My Day to demonstrate reminders
        foreach ($users->take(3) as $index => $user) {
            $task = PersonalTask::factory()->create([
                'user_id' => $user->id,
                'title' => 'My Day Focus Task '.($index + 1),
                'my_day_for' => Carbon::now()->toDateString(),
                'reminder_at' => Carbon::now()->addHours($index + 2),
                'is_important' => true,
            ]);

            PersonalTaskSubtask::factory()->create([
                'personal_task_id' => $task->id,
                'title' => 'Prepare context for '.$task->title,
            ]);
        }
    }
}
