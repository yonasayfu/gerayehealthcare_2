<?php

namespace Database\Seeders;

use App\Enums\RoleEnum;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Database\Seeder;

class StaffSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $samples = [
            ['first' => 'Yonas', 'last' => 'Sayfu', 'position' => 'Doctor'],
            ['first' => 'Sara', 'last' => 'Bekele', 'position' => 'Nurse'],
            ['first' => 'Mikael', 'last' => 'Tesfaye', 'position' => 'Caregiver'],
            ['first' => 'Lily', 'last' => 'Abebe', 'position' => 'Therapist'],
        ];

        foreach ($samples as $s) {
            $email = strtoupper(substr($s['first'], 0, 1)).preg_replace('/\s+/', '', $s['last']).'@example.com';
            $fullName = $s['first'].' '.$s['last'];

            $user = User::updateOrCreate(
                ['email' => $email],
                [
                    'name' => $fullName,
                    'password' => bcrypt('password'),
                ]
            );
            $user->assignRole(RoleEnum::STAFF->value);

            Staff::updateOrCreate(
                ['email' => $email],
                [
                    'first_name' => $s['first'],
                    'last_name' => $s['last'],
                    'phone' => '+2519'.rand(10000000, 99999999),
                    'position' => $s['position'],
                    'department' => 'Medical',
                    'role' => $s['position'],
                    'status' => 'Active',
                    'hire_date' => now()->subYears(rand(0, 5))->toDateString(),
                    'photo' => 'images/staff/placeholder.jpg',
                    'user_id' => $user->id,
                ]
            );
        }
    }
}
