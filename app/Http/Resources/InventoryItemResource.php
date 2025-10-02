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
        return [
            'id' => $this->id,
            'item_code' => $this->item_code,
            'name' => $this->name,
            'description' => $this->description,
            'category' => $this->category,
            'unit' => $this->unit,
            'quantity' => $this->quantity,
            'minimum_quantity' => $this->minimum_quantity,
            'reorder_level' => $this->reorder_level,
            'unit_price' => $this->unit_price,
            'supplier_id' => $this->supplier_id,
            'location' => $this->location,
            'barcode' => $this->barcode,
            'expiry_date' => $this->expiry_date?->toDateString(),
            'is_active' => $this->is_active,
            'notes' => $this->notes,
            
            // Computed attributes
            'is_low_stock' => $this->quantity <= $this->reorder_level,
            'stock_status' => $this->getStockStatus(),
            
            // Relationships
            'supplier' => $this->whenLoaded('supplier', function () {
                return [
                    'id' => $this->supplier->id,
                    'name' => $this->supplier->name,
                    'contact_person' => $this->supplier->contact_person,
                    'phone' => $this->supplier->phone,
                ];
            }),
            
            // Timestamps
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }

    /**
     * Get stock status
     */
    protected function getStockStatus(): string
    {
        if ($this->quantity <= 0) {
            return 'out_of_stock';
        }
        if ($this->quantity <= $this->reorder_level) {
            return 'low_stock';
        }
        if ($this->quantity <= $this->minimum_quantity) {
            return 'critical';
        }
        return 'in_stock';
    }
}
