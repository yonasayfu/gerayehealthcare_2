<?php

namespace Database\Seeders;

use App\Models\Staff;
use App\Models\TaskDelegation;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class TaskScenarioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Running Task Scenario Seeder...');

        $staff = Staff::with('user')->get();
        if ($staff->isEmpty()) {
            $this->command->error('No staff found. Please seed the staff table first.');

            return;
        }

        // Clean up old tasks
        TaskDelegation::query()->delete();

        // --- SCENARIO 1: Overdue Tasks ---
        $this->command->info('Creating overdue tasks...');
        for ($i = 0; $i < 5; $i++) {
            $creator = $staff->random();
            $assignee = $staff->random();
            TaskDelegation::create([
                'title' => 'Urgent: Review overdue report #' . ($i + 1),
                'notes' => 'This report is critical and was due last week. Please review and provide feedback immediately.',
                'created_by' => $creator->user->id,
                'assigned_to' => $assignee->id,
                'due_date' => Carbon::now()->subDays(rand(3, 10)),
                'status' => 'Pending',
                'priority_level' => 5, // Critical
            ]);
        }

        // --- SCENARIO 2: Upcoming Tasks ---
        $this->command->info('Creating upcoming tasks...');
        for ($i = 0; $i < 10; $i++) {
            $creator = $staff->random();
            $assignee = $staff->random();
            TaskDelegation::create([
                'title' => 'Prepare presentation for upcoming meeting #' . ($i + 1),
                'notes' => 'The meeting is next week. Please prepare the slides and send them for review.',
                'created_by' => $creator->user->id,
                'assigned_to' => $assignee->id,
                'due_date' => Carbon::now()->addDays(rand(2, 7)),
                'status' => 'In Progress',
                'priority_level' => rand(2, 4), // Medium to High
            ]);
        }

        // --- SCENARIO 3: Task Transfers ---
        $this->command->info('Simulating task transfers...');
        $taskToTransfer = TaskDelegation::create([
            'title' => 'Initial analysis of Q3 data',
            'notes' => 'This task will be transferred to a specialist.',
            'created_by' => $staff->random()->user->id,
            'assigned_to' => $staff->first()->id,
            'due_date' => Carbon::now()->addDays(10),
            'status' => 'Pending',
            'priority_level' => 3, // Normal
        ]);

        // Simulate the transfer
        $newAssignee = $staff->where('id', '!=', $staff->first()->id)->random();
        $taskToTransfer->update([
            'assigned_to' => $newAssignee->id,
            'notes' => 'This task has been transferred to ' . $newAssignee->first_name . ' for further analysis.',
        ]);

        // --- SCENARIO 4: Completed Tasks ---
        $this->command->info('Creating completed tasks...');
        for ($i = 0; $i < 8; $i++) {
            $creator = $staff->random();
            $assignee = $staff->random();
            TaskDelegation::create([
                'title' => 'Completed project task #' . ($i + 1),
                'notes' => 'This task was completed last month.',
                'created_by' => $creator->user->id,
                'assigned_to' => $assignee->id,
                'due_date' => Carbon::now()->subMonth(),
                'status' => 'Completed',
                'priority_level' => rand(1, 3), // Low to Normal
            ]);
        }

        // --- SCENARIO 5: KPI Scenario ---
        $this->command->info('Creating tasks for KPI analysis...');
        $kpiStaff = $staff->first();

        // High priority task completed on time
        TaskDelegation::create([
            'title' => 'KPI: High-priority task, on-time completion',
            'created_by' => $staff->random()->user->id,
            'assigned_to' => $kpiStaff->id,
            'due_date' => Carbon::now()->subDays(2),
            'status' => 'Completed',
            'priority_level' => 5,
        ]);

        // Low priority task completed late
        TaskDelegation::create([
            'title' => 'KPI: Low-priority task, late completion',
            'created_by' => $staff->random()->user->id,
            'assigned_to' => $kpiStaff->id,
            'due_date' => Carbon::now()->subDays(5),
            'status' => 'Completed',
            'priority_level' => 1,
        ]);

        $this->command->info('Task Scenario Seeder finished successfully.');
    }
}