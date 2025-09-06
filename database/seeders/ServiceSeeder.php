<?php

namespace Database\Seeders;

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
            ['name' => 'Chronic patients care and follow up', 'category' => 'Chronic Care', 'price' => 100.00],
            ['name' => 'Routine Health Care Service', 'category' => 'Routine Care', 'price' => 75.00],
            ['name' => 'Specialty care by specialists', 'category' => 'Specialty Care', 'price' => 200.00],
            ['name' => 'Special care and nursing services', 'category' => 'Nursing Services', 'price' => 120.00],
            ['name' => 'Medical Consultation Service', 'category' => 'Consultation', 'price' => 90.00],
        ]);
    }
}
