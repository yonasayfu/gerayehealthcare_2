<?php

namespace Database\Seeders;

use App\Models\Staff;
use App\Models\TaskDelegation;
use App\Models\User;
use Illuminate\Database\Seeder;

class EnhancedTaskDelegationSeeder extends Seeder
{
    public function run(): void
    {
        // Get all staff members and users
        $staffMembers = Staff::all();
        $users = User::all();
        
        if ($staffMembers->isEmpty() || $users->isEmpty()) {
            echo "No staff members or users found. Please run StaffSeeder and TestUsersSeeder first.\n";
            return;
        }

        // Scenario 1: CEO assigning tasks to various staff members
        $ceo = $users->firstWhere('email', 'ceo@gerayehealthcare.com');
        if ($ceo) {
            $this->createTasksForUser($ceo, $staffMembers, 'CEO');
        }

        // Scenario 2: COO assigning tasks to staff
        $coo = $users->firstWhere('email', 'coo@gerayehealthcare.com');
        if ($coo) {
            $this->createTasksForUser($coo, $staffMembers, 'COO');
        }

        // Scenario 3: Admin assigning tasks
        $admin = $users->firstWhere('email', 'admin@gerayehealthcare.com');
        if ($admin) {
            $this->createTasksForUser($admin, $staffMembers, 'Admin');
        }

        // Scenario 4: Staff member assigning tasks to others (peer-to-peer)
        $doctor = $users->firstWhere('email', 'doctor@gerayehealthcare.com');
        if ($doctor) {
            $this->createTasksForUser($doctor, $staffMembers, 'Doctor');
        }

        // Scenario 5: Nurse assigning tasks
        $nurse = $users->firstWhere('email', 'nurse@gerayehealthcare.com');
        if ($nurse) {
            $this->createTasksForUser($nurse, $staffMembers, 'Nurse');
        }

        // Scenario 6: Technician assigning tasks
        $technician = $users->firstWhere('email', 'technician@gerayehealthcare.com');
        if ($technician) {
            $this->createTasksForUser($technician, $staffMembers, 'Technician');
        }

        // Scenario 7: Super Admin assigning tasks
        $superAdmin = $users->firstWhere('email', 'superadmin@gerayehealthcare.com');
        if ($superAdmin) {
            $this->createTasksForUser($superAdmin, $staffMembers, 'Super Admin');
        }

        // Scenario 8: Yonas CEO assigning tasks
        $yonas = $users->firstWhere('email', 'yonas@gerayehealthcare.com');
        if ($yonas) {
            $this->createTasksForUser($yonas, $staffMembers, 'Yonas CEO');
        }

        echo "Enhanced task delegation seeding completed successfully.\n";
    }

    private function createTasksForUser($creator, $staffMembers, $roleName)
    {
        $staffCount = $staffMembers->count();
        $tasksToCreate = min(5, $staffCount); // Create up to 5 tasks or however many staff members we have

        $taskTitles = [
            "Review patient reports",
            "Update medical records",
            "Schedule follow-up appointments",
            "Prepare presentation for meeting",
            "Complete quarterly reports",
            "Organize department inventory",
            "Train new staff members",
            "Update treatment protocols",
            "Conduct patient satisfaction survey",
            "Implement new procedures",
            "Coordinate with other departments",
            "Manage staff schedules",
            "Process insurance claims",
            "Review lab results",
            "Plan team building activities"
        ];

        $taskCategories = [
            "Administrative",
            "Clinical",
            "Training",
            "Reporting",
            "Inventory",
            "Patient Care",
            "Meetings",
            "Quality Assurance"
        ];

        $statuses = ['Pending', 'In Progress', 'Completed'];
        $acceptanceStatuses = ['Pending', 'Accepted', 'Rejected'];
        $progressStatuses = ['not_started', 'in_progress', 'completed', 'blocked'];

        for ($i = 0; $i < $tasksToCreate; $i++) {
            $assignedStaff = $staffMembers->random();
            
            TaskDelegation::create([
                'title' => $roleName . ': ' . $taskTitles[array_rand($taskTitles)],
                'assigned_to' => $assignedStaff->id,
                'due_date' => now()->addDays(rand(1, 30))->format('Y-m-d'),
                'status' => $statuses[array_rand($statuses)],
                'notes' => "This is a sample task assigned by {$roleName} to test the task delegation system.",
                'created_by' => $creator->id,
                'acceptance_status' => $acceptanceStatuses[array_rand($acceptanceStatuses)],
                'response_notes' => rand(0, 1) ? "Task accepted and will be completed on time." : null,
                'responded_at' => rand(0, 1) ? now()->subDays(rand(1, 5)) : null,
                'responded_by' => rand(0, 1) ? $assignedStaff->user_id : null,
                'task_date' => now()->format('Y-m-d'),
                'start_time' => rand(0, 1) ? '09:00:00' : null,
                'end_time' => rand(0, 1) ? '17:00:00' : null,
                'estimated_duration_minutes' => rand(30, 480),
                'daily_notes' => rand(0, 1) ? "Work in progress, making good headway." : null,
                'task_category' => $taskCategories[array_rand($taskCategories)],
                'priority_level' => rand(1, 5),
                'is_billable' => rand(0, 1),
                'progress_status' => $progressStatuses[array_rand($progressStatuses)]
            ]);
        }

        echo "Created {$tasksToCreate} tasks for {$roleName}\n";
    }
}