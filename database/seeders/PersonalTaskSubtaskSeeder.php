<?php

namespace Database\Seeders;

use App\Models\PersonalTask;
use App\Models\PersonalTaskSubtask;
use Illuminate\Database\Seeder;

class PersonalTaskSubtaskSeeder extends Seeder
{
    public function run(): void
    {
        // Get all personal tasks
        $personalTasks = PersonalTask::all();

        if ($personalTasks->isEmpty()) {
            echo "No personal tasks found. Please run EnhancedPersonalTaskSeeder first.\n";
            return;
        }

        // Create subtasks for each personal task
        foreach ($personalTasks as $task) {
            // Create 1-3 subtasks for each task
            $subtaskCount = rand(1, 3);

            $subtaskTitles = [
                "Research and gather information",
                "Draft initial version",
                "Review and edit content",
                "Get feedback from colleagues",
                "Finalize and submit",
                "Schedule follow-up meeting",
                "Prepare presentation slides",
                "Send out invitations",
                "Collect required documents",
                "Update project timeline",
                "Coordinate with team members",
                "Verify data accuracy",
                "Test implementation",
                "Document process changes",
                "Train relevant staff",
            ];

            for ($i = 1; $i <= $subtaskCount; $i++) {
                PersonalTaskSubtask::create([
                    'personal_task_id' => $task->id,
                    'title' => $subtaskTitles[array_rand($subtaskTitles)],
                    'is_completed' => rand(0, 1),
                    'position' => $i,
                ]);
            }
        }

        echo "Personal task subtasks seeding completed successfully.\n";
    }
}
