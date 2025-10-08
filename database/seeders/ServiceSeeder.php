<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Service::factory()->create([
            'name' => 'General Consultation',
            'category' => 'Consultation',
            'price' => 500,
        ]);

        Service::factory()->create([
            'name' => 'Wound Dressing',
            'category' => 'Procedure',
            'price' => 300,
        ]);

        Service::factory()->create([
            'name' => 'IV-Infusion',
            'category' => 'Procedure',
            'price' => 700,
        ]);
    }
}
