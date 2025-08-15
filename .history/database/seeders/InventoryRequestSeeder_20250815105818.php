<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Staff;
use App\Models\InventoryItem;
use App\Models\InventoryRequest;
use Carbon\Carbon;
use Faker\Factory as Faker;

class InventoryRequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        if (Staff::count() === 0) {
            Staff::factory()->count(3)->create(); // Limit staff to 3
        }
        if (InventoryItem::count() === 0) {
            InventoryItem::factory()->count(3)->create(); // Limit items to 3
        }

        $staffIds = Staff::pluck('id');
        $itemIds = InventoryItem::pluck('id');

        foreach ($staffIds as $staffId) {
            // Create 1 or 2 pending inventory requests for each staff member
            for ($i = 0; $i < rand(1, 2); $i++) {
                InventoryRequest::create([
                    'requester_id' => $staffId,
                    'item_id' => $itemIds->random(),
                    'quantity_requested' => $faker->numberBetween(1, 10),
                    'reason' => $faker->sentence,
                    'status' => 'Pending',
                    'priority' => $faker->randomElement(['Normal', 'High', 'Urgent']),
                    'needed_by_date' => Carbon::now()->addDays($faker->numberBetween(5, 30))->toDateString(),
                ]);
            }
        }

        // Admin perspective: Approve or deny some of the pending requests
        $pendingRequests = InventoryRequest::where('status', 'Pending')->get();

        foreach ($pendingRequests as $request) {
            if ($faker->boolean(80)) { // 80% chance to process the request
                $newStatus = $faker->randomElement(['Approved', 'Denied']);
                $request->update([
                    'status' => $newStatus,
                    'approver_id' => $staffIds->random(),
                    'quantity_approved' => $newStatus === 'Approved' ? $request->quantity_requested : 0,
                    'approved_at' => Carbon::now(),
                ]);
            }
        }
    }
}
