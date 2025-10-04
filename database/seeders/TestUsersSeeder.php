<?php

namespace Database\Seeders;

use App\Enums\RoleEnum;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TestUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaultPassword = env('TEST_USER_PASSWORD', 'password');
        $superAdminPassword = env('TEST_SUPERADMIN_PASSWORD', 'SuperAdmin123!');

        // Create test users for each role with predictable credentials
        $testUsers = [
            [
                'name' => 'Super Admin',
                'email' => 'superadmin@gerayehealthcare.com',
                'password' => $superAdminPassword,
                'role' => RoleEnum::SUPER_ADMIN,
                'staff_data' => [
                    'first_name' => 'Super',
                    'last_name' => 'Admin',
                    'position' => 'System Administrator',
                    'department' => 'IT',
                    'phone' => '+251911000001',
                ],
            ],
            [
                'name' => 'CEO User',
                'email' => 'ceo@gerayehealthcare.com',
                'password' => $defaultPassword,
                'role' => RoleEnum::CEO,
                'staff_data' => [
                    'first_name' => 'CEO',
                    'last_name' => 'User',
                    'position' => 'Chief Executive Officer',
                    'department' => 'Executive',
                    'phone' => '+251911000002',
                ],
            ],
            [
                'name' => 'COO User',
                'email' => 'coo@gerayehealthcare.com',
                'password' => $defaultPassword,
                'role' => RoleEnum::COO,
                'staff_data' => [
                    'first_name' => 'COO',
                    'last_name' => 'User',
                    'position' => 'Chief Operating Officer',
                    'department' => 'Operations',
                    'phone' => '+251911000004',
                ],
            ],
            [
                'name' => 'Admin User',
                'email' => 'admin@gerayehealthcare.com',
                'password' => $defaultPassword,
                'role' => RoleEnum::ADMIN,
                'staff_data' => [
                    'first_name' => 'Admin',
                    'last_name' => 'User',
                    'position' => 'System Administrator',
                    'department' => 'Administration',
                    'phone' => '+251911000005',
                ],
            ],
            [
                'name' => 'Doctor User',
                'email' => 'doctor@gerayehealthcare.com',
                'password' => $defaultPassword,
                'role' => RoleEnum::STAFF,
                'staff_data' => [
                    'first_name' => 'Doctor',
                    'last_name' => 'User',
                    'position' => 'Senior Doctor',
                    'department' => 'Clinical',
                    'phone' => '+251911000007',
                ],
            ],
            [
                'name' => 'Nurse User',
                'email' => 'nurse@gerayehealthcare.com',
                'password' => $defaultPassword,
                'role' => RoleEnum::STAFF,
                'staff_data' => [
                    'first_name' => 'Nurse',
                    'last_name' => 'User',
                    'position' => 'Senior Nurse',
                    'department' => 'Clinical',
                    'phone' => '+251911000008',
                ],
            ],
            [
                'name' => 'Staff User',
                'email' => 'staff@gerayehealthcare.com',
                'password' => $defaultPassword,
                'role' => RoleEnum::STAFF,
                'staff_data' => [
                    'first_name' => 'Staff',
                    'last_name' => 'User',
                    'position' => 'Senior Nurse',
                    'department' => 'Emergency',
                    'phone' => '+251911000006',
                ],
            ],
            [
                'name' => 'Guest User',
                'email' => 'guest@gerayehealthcare.com',
                'password' => $defaultPassword,
                'role' => RoleEnum::GUEST,
                'staff_data' => null, // Guests don't have staff records
            ],
        ];

        foreach ($testUsers as $userData) {
            // Create or update user
            $user = User::updateOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => Hash::make($userData['password']),
                    'email_verified_at' => now(),
                ]
            );

            // Assign role
            $user->syncRoles([$userData['role']->value]);

            // Create staff record if provided
            if ($userData['staff_data']) {
                Staff::updateOrCreate(
                    ['user_id' => $user->id],
                    array_merge($userData['staff_data'], [
                        'email' => $userData['email'],
                        'role' => $userData['staff_data']['position'],
                        'status' => 'Active',
                        'hire_date' => now()->subYears(rand(1, 5))->toDateString(),
                        'hourly_rate' => $this->getHourlyRateByPosition($userData['staff_data']['position']),
                    ])
                );
            }
        }
    }

    /**
     * Get hourly rate based on position
     */
    private function getHourlyRateByPosition(string $position): float
    {
        $rates = [
            'Chief Executive Officer' => 150.00,
            'Chief Operating Officer' => 130.00,
            'System Administrator' => 80.00,
            'Senior Doctor' => 120.00,
            'Senior Nurse' => 60.00,
        ];

        return $rates[$position] ?? 50.00;
    }
}
