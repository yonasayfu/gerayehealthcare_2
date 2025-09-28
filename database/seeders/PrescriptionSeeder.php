<?php

namespace Database\Seeders;

use App\Models\Prescription;
use App\Models\PrescriptionItem;
use Illuminate\Database\Seeder;

class PrescriptionSeeder extends Seeder
{
    public function run(): void
    {
        // Create base prescriptions
        Prescription::factory()->count(30)->create();

        // Attach items to each prescription
        $prescriptionIds = Prescription::pluck('id');
        foreach ($prescriptionIds as $pid) {
            PrescriptionItem::factory()->count(rand(1, 4))->create([
                'prescription_id' => $pid,
            ]);
        }
    }
}
