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
        // Create test users for each role with predictable credentials
        $testUsers = [
            [
                'name' => 'Super Administrator',
                'email' => 'superadmin@gerayehealthcare.com',
                'password' => 'SuperAdmin123!',
                'role' => RoleEnum::SUPER_ADMIN,
                'staff_data' => [
                    'first_name' => 'Super',
                    'last_name' => 'Administrator',
                    'position' => 'System Administrator',
                    'department' => 'IT',
                    'phone' => '+251911000001',
                ],
            ],
            [
                'name' => 'Dr. Sarah Johnson',
                'email' => 'ceo@gerayehealthcare.com',
                'password' => 'CEO123!',
                'role' => RoleEnum::CEO,
                'staff_data' => [
                    'first_name' => 'Sarah',
                    'last_name' => 'Johnson',
                    'position' => 'Chief Executive Officer',
                    'department' => 'Executive',
                    'phone' => '+251911000002',
                ],
            ],
            [
                'name' => 'Dr. Michael Chen',
                'email' => 'coo@gerayehealthcare.com',
                'password' => 'COO123!',
                'role' => RoleEnum::COO,
                'staff_data' => [
                    'first_name' => 'Michael',
                    'last_name' => 'Chen',
                    'position' => 'Chief Operating Officer',
                    'department' => 'Operations',
                    'phone' => '+251911000003',
                ],
            ],
            [
                'name' => 'Emily Rodriguez',
                'email' => 'admin@gerayehealthcare.com',
                'password' => 'Admin123!',
                'role' => RoleEnum::ADMIN,
                'staff_data' => [
                    'first_name' => 'Emily',
                    'last_name' => 'Rodriguez',
                    'position' => 'System Administrator',
                    'department' => 'Administration',
                    'phone' => '+251911000004',
                ],
            ],
            [
                'name' => 'Dr. James Wilson',
                'email' => 'doctor@gerayehealthcare.com',
                'password' => 'Doctor123!',
                'role' => RoleEnum::STAFF,
                'staff_data' => [
                    'first_name' => 'James',
                    'last_name' => 'Wilson',
                    'position' => 'Senior Doctor',
                    'department' => 'Cardiology',
                    'phone' => '+251911000005',
                ],
            ],
            [
                'name' => 'Nurse Lisa Brown',
                'email' => 'nurse@gerayehealthcare.com',
                'password' => 'Nurse123!',
                'role' => RoleEnum::STAFF,
                'staff_data' => [
                    'first_name' => 'Lisa',
                    'last_name' => 'Brown',
                    'position' => 'Senior Nurse',
                    'department' => 'Emergency',
                    'phone' => '+251911000006',
                ],
            ],
            [
                'name' => 'David Kim',
                'email' => 'technician@gerayehealthcare.com',
                'password' => 'Tech123!',
                'role' => RoleEnum::STAFF,
                'staff_data' => [
                    'first_name' => 'David',
                    'last_name' => 'Kim',
                    'position' => 'Lab Technician',
                    'department' => 'Laboratory',
                    'phone' => '+251911000007',
                ],
            ],
            [
                'name' => 'Guest User',
                'email' => 'guest@gerayehealthcare.com',
                'password' => 'Guest123!',
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

        // Create additional staff members for testing
        $this->createAdditionalStaff();
    }

    /**
     * Create additional staff members for testing purposes
     */
    private function createAdditionalStaff(): void
    {
        $additionalStaff = [
            [
                'name' => 'Dr. Anna Martinez',
                'email' => 'anna.martinez@gerayehealthcare.com',
                'position' => 'Pediatrician',
                'department' => 'Pediatrics',
            ],
            [
                'name' => 'Dr. Robert Taylor',
                'email' => 'robert.taylor@gerayehealthcare.com',
                'position' => 'Orthopedic Surgeon',
                'department' => 'Orthopedics',
            ],
            [
                'name' => 'Nurse Jennifer Davis',
                'email' => 'jennifer.davis@gerayehealthcare.com',
                'position' => 'ICU Nurse',
                'department' => 'Intensive Care',
            ],
            [
                'name' => 'Dr. Ahmed Hassan',
                'email' => 'ahmed.hassan@gerayehealthcare.com',
                'position' => 'Neurologist',
                'department' => 'Neurology',
            ],
            [
                'name' => 'Maria Santos',
                'email' => 'maria.santos@gerayehealthcare.com',
                'position' => 'Pharmacist',
                'department' => 'Pharmacy',
            ],
        ];

        foreach ($additionalStaff as $staffData) {
            $user = User::updateOrCreate(
                ['email' => $staffData['email']],
                [
                    'name' => $staffData['name'],
                    'password' => Hash::make('Staff123!'),
                    'email_verified_at' => now(),
                ]
            );

            $user->syncRoles([RoleEnum::STAFF->value]);

            $nameParts = explode(' ', $staffData['name']);
            $firstName = $nameParts[0];
            $lastName = implode(' ', array_slice($nameParts, 1));

            Staff::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'email' => $staffData['email'],
                    'phone' => '+25191'.rand(1000000, 9999999),
                    'position' => $staffData['position'],
                    'department' => $staffData['department'],
                    'role' => $staffData['position'],
                    'status' => 'Active',
                    'hire_date' => now()->subYears(rand(1, 8))->toDateString(),
                    'hourly_rate' => $this->getHourlyRateByPosition($staffData['position']),
                ]
            );
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
            'Pediatrician' => 110.00,
            'Orthopedic Surgeon' => 140.00,
            'Neurologist' => 130.00,
            'Senior Nurse' => 60.00,
            'ICU Nurse' => 70.00,
            'Lab Technician' => 45.00,
            'Pharmacist' => 55.00,
        ];

        return $rates[$position] ?? 50.00;
    }
}
