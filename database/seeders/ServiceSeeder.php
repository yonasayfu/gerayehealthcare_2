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
        ]);
    }
}
