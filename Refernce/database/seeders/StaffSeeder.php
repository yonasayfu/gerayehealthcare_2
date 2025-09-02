<?php

namespace Database\Seeders;

use App\Models\Staff;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class StaffSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 7 example staff members with diverse profiles
        $staffMembers = [
            [
                'employee_id' => 'EMP'.date('Y').'0001',
                'first_name' => 'John',
                'last_name' => 'Doe',
                'email' => 'john.doe@company.com',
                'phone_number' => '+1-555-123-4567',
                'position' => 'Senior Software Engineer',
                'department' => 'Engineering',
                'hire_date' => now()->subYears(3),
                'salary' => 95000,
                'employment_type' => 'full-time',
                'status' => 'active',
                'is_active' => true,
                'address' => '123 Tech Street, San Francisco, CA 94105',
                'emergency_contact_name' => 'Jane Doe',
                'emergency_contact_phone' => '+1-555-987-6543',
                'notes' => 'Lead developer with expertise in Laravel and Vue.js. Mentors junior developers.',
            ],
            [
                'employee_id' => 'EMP'.date('Y').'0002',
                'first_name' => 'Sarah',
                'last_name' => 'Johnson',
                'email' => 'sarah.johnson@company.com',
                'phone_number' => '+1-555-234-5678',
                'position' => 'Product Manager',
                'department' => 'Product',
                'hire_date' => now()->subYears(2),
                'salary' => 85000,
                'employment_type' => 'full-time',
                'status' => 'active',
                'is_active' => true,
                'address' => '456 Innovation Ave, Austin, TX 78701',
                'emergency_contact_name' => 'Mike Johnson',
                'emergency_contact_phone' => '+1-555-876-5432',
                'notes' => 'Experienced product manager with strong analytical skills and user-focused approach.',
            ],
            [
                'employee_id' => 'EMP'.date('Y').'0003',
                'first_name' => 'Michael',
                'last_name' => 'Chen',
                'email' => 'michael.chen@company.com',
                'phone_number' => '+1-555-345-6789',
                'position' => 'UI/UX Designer',
                'department' => 'Design',
                'hire_date' => now()->subYear(),
                'salary' => 70000,
                'employment_type' => 'full-time',
                'status' => 'active',
                'is_active' => true,
                'address' => '789 Creative Blvd, Seattle, WA 98101',
                'emergency_contact_name' => 'Lisa Chen',
                'emergency_contact_phone' => '+1-555-765-4321',
                'notes' => 'Creative designer with expertise in modern UI/UX principles and user research.',
            ],
            [
                'employee_id' => 'EMP'.date('Y').'0004',
                'first_name' => 'Emily',
                'last_name' => 'Rodriguez',
                'email' => 'emily.rodriguez@company.com',
                'phone_number' => '+1-555-456-7890',
                'position' => 'Marketing Specialist',
                'department' => 'Marketing',
                'hire_date' => now()->subMonths(8),
                'salary' => 55000,
                'employment_type' => 'part-time',
                'status' => 'active',
                'is_active' => true,
                'address' => '321 Marketing Way, Denver, CO 80202',
                'emergency_contact_name' => 'Carlos Rodriguez',
                'emergency_contact_phone' => '+1-555-654-3210',
                'notes' => 'Digital marketing specialist focusing on social media and content strategy.',
            ],
            [
                'employee_id' => 'EMP'.date('Y').'0005',
                'first_name' => 'David',
                'last_name' => 'Wilson',
                'email' => 'david.wilson@company.com',
                'phone_number' => '+1-555-567-8901',
                'position' => 'DevOps Engineer',
                'department' => 'Engineering',
                'hire_date' => now()->subMonths(6),
                'salary' => 88000,
                'employment_type' => 'contract',
                'status' => 'active',
                'is_active' => true,
                'address' => '654 Cloud Street, Portland, OR 97201',
                'emergency_contact_name' => 'Susan Wilson',
                'emergency_contact_phone' => '+1-555-543-2109',
                'notes' => 'DevOps specialist with expertise in AWS, Docker, and CI/CD pipelines.',
            ],
            [
                'employee_id' => 'EMP'.date('Y').'0006',
                'first_name' => 'Amanda',
                'last_name' => 'Taylor',
                'email' => 'amanda.taylor@company.com',
                'phone_number' => '+1-555-678-9012',
                'position' => 'Junior Developer',
                'department' => 'Engineering',
                'hire_date' => now()->subMonths(3),
                'salary' => 45000,
                'employment_type' => 'intern',
                'status' => 'active',
                'is_active' => true,
                'address' => '987 Learning Lane, Boston, MA 02101',
                'emergency_contact_name' => 'Robert Taylor',
                'emergency_contact_phone' => '+1-555-432-1098',
                'notes' => 'Promising intern with strong programming fundamentals and eagerness to learn.',
            ],
            [
                'employee_id' => 'EMP'.date('Y').'0007',
                'first_name' => 'James',
                'last_name' => 'Brown',
                'email' => 'james.brown@company.com',
                'phone_number' => '+1-555-789-0123',
                'position' => 'Sales Manager',
                'department' => 'Sales',
                'hire_date' => now()->subYears(4),
                'salary' => 75000,
                'employment_type' => 'full-time',
                'status' => 'on-leave',
                'is_active' => true,
                'address' => '147 Sales Drive, Chicago, IL 60601',
                'emergency_contact_name' => 'Mary Brown',
                'emergency_contact_phone' => '+1-555-321-0987',
                'notes' => 'Experienced sales manager currently on paternity leave. Expected return next month.',
            ],
        ];

        foreach ($staffMembers as $staffData) {
            $staff = Staff::create($staffData);

            // Create associated user account
            $user = User::factory()->create([
                'name' => $staff->full_name,
                'email' => $staff->email,
                'phone_number' => $staff->phone_number,
                'password' => Hash::make('password'),
                'is_active' => $staff->is_active,
            ]);

            $user->assignRole('staff');
            $staff->update(['user_id' => $user->id]);
        }

        $this->command->info('Staff seeder completed successfully!');
        $this->command->info('Created 7 example staff members with diverse profiles');
        $this->command->info('Total staff created: '.Staff::count());
        $this->command->info('All staff members have user accounts with password: "password"');
    }
}
