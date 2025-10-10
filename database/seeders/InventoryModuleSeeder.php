<?php

namespace Database\Seeders;

use App\Models\InventoryAlert;
use App\Models\InventoryItem;
use App\Models\InventoryMaintenanceRecord;
use App\Models\InventoryRequest;
use App\Models\InventoryTransaction;
use App\Models\Patient;
use App\Models\Staff;
use App\Models\Supplier;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class InventoryModuleSeeder extends Seeder
{
    /**
     * Seed inventory catalogue, requests, transactions, and alerts.
     */
    public function run(): void
    {
        $suppliers = Supplier::factory()->count(5)->create();
        $staffIds = Staff::pluck('id');
        $patientIds = Patient::pluck('id');

        if ($staffIds->isEmpty()) {
            $staffIds = Staff::factory()->count(3)->create()->pluck('id');
        }

        if ($patientIds->isEmpty()) {
            $patientIds = Patient::factory()->count(3)->create()->pluck('id');
        }

        $items = collect(range(1, 5))->map(function ($i) use ($suppliers, $staffIds, $patientIds) {
            $assignedType = $i % 3 === 0 ? 'patient' : 'staff';
            $assignedId = $assignedType === 'patient'
                ? $patientIds[$i % $patientIds->count()]
                : $staffIds[$i % $staffIds->count()];

            return InventoryItem::factory()->create([
                'name' => 'Inventory Item '.$i,
                'supplier_id' => $suppliers[$i % $suppliers->count()]->id,
                'assigned_to_type' => $assignedType,
                'assigned_to_id' => $assignedId,
                'status' => $i % 2 === 0 ? 'In Use' : 'Available',
                'quantity_on_hand' => 10 - $i,
                'reorder_level' => 5,
            ]);
        });

        foreach ($items as $index => $item) {
            $requesterId = $staffIds[$index % $staffIds->count()];
            $approverId = $staffIds[($index + 1) % $staffIds->count()];

            $request = InventoryRequest::factory()->create([
                'item_id' => $item->id,
                'requester_id' => $requesterId,
                'approver_id' => $index % 2 === 0 ? $approverId : null,
                'quantity_requested' => max(1, 2 + $index),
                'quantity_approved' => $index % 2 === 0 ? max(1, 2 + $index) : null,
                'status' => $index % 2 === 0 ? 'Approved' : 'Pending',
                'priority' => $index % 3 === 0 ? 'High' : 'Normal',
                'needed_by_date' => Carbon::now()->addDays($index + 5)->toDateString(),
                'approved_at' => $index % 2 === 0 ? Carbon::now()->subDays($index) : null,
                'fulfilled_at' => $index % 2 === 0 ? Carbon::now()->subDays(max(0, $index - 1)) : null,
            ]);

            InventoryTransaction::factory()->create([
                'item_id' => $item->id,
                'request_id' => $request->id,
                'transaction_type' => $index % 2 === 0 ? 'Issue' : 'Return',
                'from_location' => 'Central Warehouse',
                'to_location' => $item->assigned_to_type === 'staff' ? 'Home Care Kit' : 'Patient Residence',
                'from_assigned_to_type' => 'supplier',
                'from_assigned_to_id' => $item->supplier_id,
                'to_assigned_to_type' => $item->assigned_to_type,
                'to_assigned_to_id' => $item->assigned_to_id,
                'quantity' => max(1, min(5, $item->quantity_on_hand)),
                'performed_by_id' => $approverId,
            ]);

            InventoryMaintenanceRecord::factory()->create([
                'item_id' => $item->id,
                'status' => $index % 2 === 0 ? 'Completed' : 'Scheduled',
                'scheduled_date' => Carbon::now()->addDays($index + 1)->toDateString(),
                'actual_date' => $index % 2 === 0 ? Carbon::now()->addDays($index + 2)->toDateString() : null,
                'next_due_date' => Carbon::now()->addMonths(1)->toDateString(),
                'performed_by_staff_id' => $requesterId,
            ]);

            InventoryAlert::factory()->create([
                'item_id' => $item->id,
                'alert_type' => $item->quantity_on_hand < $item->reorder_level ? 'Low Stock' : 'Maintenance',
                'is_active' => $index % 2 === 0,
                'message' => Str::title($item->name).' alert generated for demo dataset.',
            ]);
        }
    }
}
