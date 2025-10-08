<?php

namespace Database\Seeders;

use App\Models\Patient;
use App\Models\User;
use Illuminate\Database\Seeder;

class PatientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $patientUser = User::where('email', 'patient@geraye.com')->first();

        if ($patientUser) {
            Patient::updateOrCreate(
                ['email' => $patientUser->email],
                [
                    'full_name' => 'Test Patient',
                    'phone_number' => '1234567890',
                ]
            );
        }
    }
}
