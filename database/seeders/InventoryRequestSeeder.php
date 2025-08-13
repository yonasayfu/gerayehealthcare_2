<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Staff;
use App\Models\InventoryItem;
use App\Models\InventoryRequest;
use Carbon\Carbon;

class InventoryRequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure staff and inventory items exist
        if (Staff::count() === 0) {
            Staff::factory()->count(5)->create();
        }
        if (InventoryItem::count() === 0) {
            InventoryItem::factory()->count(10)->create();
        }

        $staffIds = Staff::pluck('id');
        $itemIds = InventoryItem::pluck('id');

        foreach ($staffIds as $staffId) {
            // Create 3-5 random inventory requests for each staff member
            for ($i = 0; $i < rand(3, 5); $i++) {
                $requesterId = $staffId;
                $approverId = $staffIds->random();
                $itemId = $itemIds->random();

                InventoryRequest::create([
                    'requester_id' => $requesterId,
                    'approver_id' => $approverId,
                    'item_id' => $itemId,
                    'quantity_requested' => rand(1, 5),
                    'quantity_approved' => rand(1, 5),
                    'reason' => $this->faker()->sentence(),
                    'status' => collect(['Pending', 'Approved', 'Denied', 'Fulfilled'])->random(),
                    'priority' => collect(['Normal', 'High', 'Urgent'])->random(),
                    'needed_by_date' => Carbon::now()->addDays(rand(1, 14)),
                    'approved_at' => rand(0, 1) ? Carbon::now()->subDays(rand(1, 7)) : null,
                    'fulfilled_at' => rand(0, 1) ? Carbon::now()->subDays(rand(1, 3)) : null,
                ]);
            }
        }
    }

    protected function faker()
    {
        return \Faker\Factory::create();
    }
}
