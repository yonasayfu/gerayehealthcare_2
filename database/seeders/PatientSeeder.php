<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class PatientSeeder extends Seeder
{
    public function run(): void
    {
        $samples = [
            ['first' => 'Daniel', 'last' => 'Kebede'],
            ['first' => 'Lulit', 'last' => 'Hailemariam'],
            ['first' => 'Biruk', 'last' => 'Gebre'],
        ];

        $staffIds = \App\Models\Staff::pluck('id')->all();
        $idx = 0;

        foreach ($samples as $s) {
            $email = strtoupper(substr($s['first'], 0, 1)).preg_replace('/\s+/', '', $s['last']).'@example.com';
            $fullName = $s['first'].' '.$s['last'];

            $user = \App\Models\User::updateOrCreate(
                ['email' => $email],
                [
                    'name' => $fullName,
                    'password' => bcrypt('password'),
                ]
            );

            \App\Models\Patient::updateOrCreate(
                ['email' => $email],
                [
                    'full_name' => $fullName,
                    'date_of_birth' => now()->subYears(rand(20, 65))->subDays(rand(0, 365))->toDateString(),
                    'gender' => rand(0, 1) ? 'Male' : 'Female',
                    'address' => 'Addis Ababa',
                    'phone_number' => '+2519'.rand(10000000, 99999999),
                    'emergency_contact' => 'EC '.rand(10000000, 99999999),
                    'source' => 'Walk-in',
                    'user_id' => $user->id,
                    'registered_by_staff_id' => $staffIds[$idx % max(1, count($staffIds))] ?? null,
                ]
            );

            $idx++;
        }
    }
}
