<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('services')->insert([
            ['name' => 'Chronic patients care and follow up', 'price' => 100.00],
            ['name' => 'Routine Health Care Service', 'price' => 75.00],
            ['name' => 'Specialty care by specialists', 'price' => 200.00],
            ['name' => 'Special care and nursing services', 'price' => 120.00],
            ['name' => 'Medical Consultation Service', 'price' => 90.00],
            ['name' => 'Basic nursing care/ 24hr Nurse care', 'price' => 1500.00],
            ['name' => 'Emergency first aid', 'price' => 180.00],
            ['name' => 'Child and Maternal Care', 'price' => 110.00],
            ['name' => 'Medication administration, advice, and follow up', 'price' => 60.00],
            ['name' => 'Emotional and Psychological care', 'price' => 130.00],
            ['name' => 'Acu Puncture', 'price' => 80.00],
            ['name' => 'Preventive care services', 'price' => 70.00],
            ['name' => 'Nutrition management and support', 'price' => 95.00],
            ['name' => 'Palliative care', 'price' => 140.00],
            ['name' => 'Health education and promotion', 'price' => 50.00],
            ['name' => 'Screening and regular medical check-up', 'price' => 250.00],
        ]);
    }
}
