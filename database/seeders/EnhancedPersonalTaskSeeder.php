<?php

namespace Database\Seeders;

use App\Models\PersonalTask;
use App\Models\User;
use Illuminate\Database\Seeder;

class EnhancedPersonalTaskSeeder extends Seeder
{
    public function run(): void
    {
        // Get all users
        $users = User::all();

        if ($users->isEmpty()) {
            echo "No users found. Please run TestUsersSeeder first.\n";
            return;
        }

        // Scenario 1: CEO personal tasks
        $ceo = $users->firstWhere('email', 'ceo@gerayehealthcare.com');
        if ($ceo) {
            $this->createPersonalTasksForUser($ceo, 'CEO');
        }

        // Scenario 2: COO personal tasks
        $coo = $users->firstWhere('email', 'coo@gerayehealthcare.com');
        if ($coo) {
            $this->createPersonalTasksForUser($coo, 'COO');
        }

        // Scenario 3: Admin personal tasks
        $admin = $users->firstWhere('email', 'admin@gerayehealthcare.com');
        if ($admin) {
            $this->createPersonalTasksForUser($admin, 'Admin');
        }

        // Scenario 4: Doctor personal tasks
        $doctor = $users->firstWhere('email', 'doctor@gerayehealthcare.com');
        if ($doctor) {
            $this->createPersonalTasksForUser($doctor, 'Doctor');
        }

        // Scenario 5: Nurse personal tasks
        $nurse = $users->firstWhere('email', 'nurse@gerayehealthcare.com');
        if ($nurse) {
            $this->createPersonalTasksForUser($nurse, 'Nurse');
        }

        // Scenario 6: Technician personal tasks
        $technician = $users->firstWhere('email', 'technician@gerayehealthcare.com');
        if ($technician) {
            $this->createPersonalTasksForUser($technician, 'Technician');
        }

        // Scenario 7: Super Admin personal tasks
        $superAdmin = $users->firstWhere('email', 'superadmin@gerayehealthcare.com');
        if ($superAdmin) {
            $this->createPersonalTasksForUser($superAdmin, 'Super Admin');
        }

        // Scenario 8: Yonas CEO personal tasks
        $yonas = $users->firstWhere('email', 'yonas@gerayehealthcare.com');
        if ($yonas) {
            $this->createPersonalTasksForUser($yonas, 'Yonas CEO');
        }

        echo "Enhanced personal task seeding completed successfully.\n";
    }

    private function createPersonalTasksForUser($user, $roleName)
    {
        $taskTitles = [
            "Review quarterly reports",
            "Schedule team meeting",
            "Update professional certifications",
            "Plan vacation time",
            "Organize desk and files",
            "Research new industry trends",
            "Follow up with mentors",
            "Complete continuing education",
            "Update personal website",
            "Network with industry peers",
            "Prepare presentation materials",
            "Respond to important emails",
            "Review budget allocations",
            "Plan professional development",
            "Update contact database",
        ];

        $taskCategories = [
            "Work",
            "Professional Development",
            "Personal",
            "Health",
            "Finance",
            "Learning",
            "Communication",
            "Planning",
        ];

        // Create regular tasks
        for ($i = 0; $i < 5; $i++) {
            // Randomly decide if this task should have time tracking data
            $hasTimeTracking = rand(0, 1);

            PersonalTask::create([
                'user_id' => $user->id,
                'title' => $roleName . ': ' . $taskTitles[array_rand($taskTitles)],
                'notes' => "This is a sample personal task for {$roleName} to test the personal task system.",
                'due_date' => now()->addDays(rand(1, 30)),
                'is_completed' => rand(0, 1),
                'is_important' => rand(0, 1),
                'reminder_at' => rand(0, 1) ? now()->addDays(rand(1, 7)) : null,
                'reminded_at' => null,
                'my_day_for' => rand(0, 1) ? now()->addDays(rand(-2, 2))->format('Y-m-d') : null,
                'recurrence_type' => 'none', // Set to 'none' instead of null
                'recurrence_weekdays' => null,
                'task_date' => now()->format('Y-m-d'),
                'start_time' => $hasTimeTracking ? '09:00:00' : null,
                'end_time' => $hasTimeTracking ? '17:00:00' : null,
                'estimated_duration_minutes' => rand(30, 240),
                'daily_notes' => rand(0, 1) ? "Making progress on this task." : null,
                'task_category' => $taskCategories[array_rand($taskCategories)],
                'priority_level' => rand(1, 5),
                'is_billable' => rand(0, 1),
            ]);
        }

        // Create some recurring tasks
        PersonalTask::create([
            'user_id' => $user->id,
            'title' => $roleName . ': Weekly team check-in',
            'notes' => "Weekly check-in with team members to discuss progress and challenges.",
            'due_date' => null,
            'is_completed' => false,
            'is_important' => true,
            'reminder_at' => now()->addDay(), // Tomorrow
            'reminded_at' => null,
            'my_day_for' => now()->format('Y-m-d'),
            'recurrence_type' => 'weekly', // Set to 'weekly' for recurring tasks
            'recurrence_weekdays' => json_encode(['monday']), // Properly encode as JSON
            'task_date' => now()->format('Y-m-d'),
            'start_time' => '10:00:00',
            'end_time' => '11:00:00',
            'estimated_duration_minutes' => 60,
            'daily_notes' => null,
            'task_category' => 'Work',
            'priority_level' => 4,
            'is_billable' => false,
        ]);

        PersonalTask::create([
            'user_id' => $user->id,
            'title' => $roleName . ': Monthly performance review',
            'notes' => "Monthly self-assessment of performance and goal tracking.",
            'due_date' => null,
            'is_completed' => false,
            'is_important' => true,
            'reminder_at' => now()->addWeek(), // Next week
            'reminded_at' => null,
            'my_day_for' => null,
            'recurrence_type' => 'none', // Set to 'none' for non-recurring tasks
            'recurrence_weekdays' => null,
            'task_date' => now()->format('Y-m-d'),
            'start_time' => '14:00:00',
            'end_time' => '15:30:00',
            'estimated_duration_minutes' => 90,
            'daily_notes' => null,
            'task_category' => 'Professional Development',
            'priority_level' => 5,
            'is_billable' => false,
        ]);

        echo "Created personal tasks for {$roleName}\n";
    }
}
