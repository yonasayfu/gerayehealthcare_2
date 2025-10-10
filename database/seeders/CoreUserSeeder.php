<?php

namespace Database\Seeders;

use App\Enums\RoleEnum;
use App\Models\Patient;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class CoreUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaultPassword = env('TEST_USER_PASSWORD', 'password');
        $superAdminPassword = env('TEST_SUPERADMIN_PASSWORD', 'SuperAdmin123!');

        $users = [
            [
                'name' => 'Super Admin',
                'email' => 'superadmin@gerayehealthcare.com',
                'password' => $superAdminPassword,
                'role' => RoleEnum::SUPER_ADMIN,
                'staff' => [
                    'first_name' => 'Super',
                    'last_name' => 'Admin',
                    'position' => 'Chief Technology Officer',
                    'department' => 'IT',
                    'phone' => '+251911000001',
                ],
            ],
            [
                'name' => 'CEO User',
                'email' => 'ceo@gerayehealthcare.com',
                'password' => $defaultPassword,
                'role' => RoleEnum::CEO,
                'staff' => [
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
                'staff' => [
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
                'staff' => [
                    'first_name' => 'Admin',
                    'last_name' => 'User',
                    'position' => 'Systems Administrator',
                    'department' => 'Administration',
                    'phone' => '+251911000005',
                ],
            ],
            [
                'name' => 'Doctor User',
                'email' => 'doctor@gerayehealthcare.com',
                'password' => $defaultPassword,
                'role' => RoleEnum::DOCTOR,
                'staff' => [
                    'first_name' => 'Daniel',
                    'last_name' => 'Bekele',
                    'position' => 'Senior Doctor',
                    'department' => 'Clinical',
                    'phone' => '+251911000007',
                ],
            ],
            [
                'name' => 'Nurse User',
                'email' => 'nurse@gerayehealthcare.com',
                'password' => $defaultPassword,
                'role' => RoleEnum::NURSE,
                'staff' => [
                    'first_name' => 'Nardos',
                    'last_name' => 'Abebe',
                    'position' => 'Lead Nurse',
                    'department' => 'Clinical',
                    'phone' => '+251911000008',
                ],
            ],
            [
                'name' => 'Staff User',
                'email' => 'staff@gerayehealthcare.com',
                'password' => $defaultPassword,
                'role' => RoleEnum::STAFF,
                'staff' => [
                    'first_name' => 'Selam',
                    'last_name' => 'Hailemariam',
                    'position' => 'Field Nurse',
                    'department' => 'Home Care',
                    'phone' => '+251911000006',
                ],
            ],
            [
                'name' => 'Patient Demo',
                'email' => 'patient@gerayehealthcare.com',
                'password' => $defaultPassword,
                'role' => RoleEnum::PATIENT,
                'patient' => [
                    'full_name' => 'Patient Demo',
                    'gender' => 'Female',
                    'phone_number' => '+251911000009',
                    'address' => 'Bole, Addis Ababa',
                    'city' => 'Addis Ababa',
                    'state' => 'Addis Ababa',
                    'country' => 'Ethiopia',
                    'preferred_language' => 'English',
                    'blood_type' => 'O+',
                    'status' => 'Active',
                    'source' => 'Mobile Onboarding',
                    'emergency_contact_name' => 'Marta Bekele',
                    'emergency_contact_phone' => '+251911000010',
                    'emergency_contact_relationship' => 'Sister',
                ],
            ],
            [
                'name' => 'Guest User',
                'email' => 'guest@gerayehealthcare.com',
                'password' => $defaultPassword,
                'role' => RoleEnum::GUEST,
            ],
        ];

        foreach ($users as $userData) {
            $user = User::updateOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => Hash::make($userData['password']),
                    'email_verified_at' => now(),
                ]
            );

            $user->syncRoles([$userData['role']->value]);

            if (!empty($userData['staff'])) {
                $staffData = $userData['staff'];
                Staff::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'first_name' => $staffData['first_name'],
                        'last_name' => $staffData['last_name'],
                        'email' => $userData['email'],
                        'phone' => $staffData['phone'],
                        'position' => $staffData['position'],
                        'department' => $staffData['department'],
                        'role' => $staffData['position'],
                        'status' => 'Active',
                        'hire_date' => Carbon::now()->subYears(3)->toDateString(),
                        'hourly_rate' => $this->getHourlyRateByPosition($staffData['position']),
                    ]
                );
            }

            if (!empty($userData['patient'])) {
                $patientData = $userData['patient'];
                $registeredBy = Staff::where('email', 'doctor@gerayehealthcare.com')->first()
                    ?? Staff::whereNotNull('id')->first();

                Patient::updateOrCreate(
                    ['user_id' => $user->id],
                    array_merge(
                        $patientData,
                        [
                            'user_id' => $user->id,
                            'email' => $userData['email'],
                            'fayda_id' => 'GHC-'.now()->format('ymd').'-'.$user->id,
                            'date_of_birth' => Carbon::now()->subYears(32)->subMonths(3)->toDateString(),
                            'acquisition_date' => now()->subDays(10),
                            'registered_by_staff_id' => $registeredBy?->id,
                        ]
                    )
                );
            }
        }
    }

    private function getHourlyRateByPosition(string $position): float
    {
        $rates = [
            'Chief Technology Officer' => 150.00,
            'Chief Executive Officer' => 160.00,
            'Chief Operating Officer' => 145.00,
            'Systems Administrator' => 85.00,
            'Senior Doctor' => 125.00,
            'Lead Nurse' => 65.00,
            'Field Nurse' => 55.00,
        ];

        return $rates[$position] ?? 50.00;
    }
}
