<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InventoryItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $quantity = $this->quantity_on_hand ?? 0;
        $reorderLevel = $this->reorder_level ?? 0;

        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'category' => $this->item_category,
            'type' => $this->item_type,
            'serial_number' => $this->serial_number,
            'status' => $this->status,
            'quantity_on_hand' => $quantity,
            'reorder_level' => $reorderLevel,
            'is_low_stock' => $quantity <= $reorderLevel,
            'purchase_date' => $this->purchase_date?->toDateString(),
            'warranty_expiry' => $this->warranty_expiry?->toDateString(),
            'assigned_to_type' => $this->assigned_to_type,
            'assigned_to_id' => $this->assigned_to_id,
            'last_maintenance_date' => $this->last_maintenance_date?->toDateString(),
            'next_maintenance_due' => $this->next_maintenance_due?->toDateString(),
            'maintenance_schedule' => $this->maintenance_schedule,
            'notes' => $this->notes,
            'supplier_id' => $this->supplier_id,
            'supplier' => $this->whenLoaded('supplier', function () {
                return [
                    'id' => $this->supplier->id,
                    'name' => $this->supplier->name,
                    'contact_person' => $this->supplier->contact_person,
                    'phone' => $this->supplier->phone,
                    'email' => $this->supplier->email,
                ];
            }),
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }
}
