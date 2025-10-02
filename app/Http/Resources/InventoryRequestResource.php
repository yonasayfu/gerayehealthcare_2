<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InventoryRequestResource extends JsonResource
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
            'request_code' => $this->request_code,
            'requested_by' => $this->requested_by,
            'inventory_item_id' => $this->inventory_item_id,
            'quantity_requested' => $this->quantity_requested,
            'quantity_approved' => $this->quantity_approved,
            'purpose' => $this->purpose,
            'priority' => $this->priority,
            'status' => $this->status,
            'requested_date' => $this->requested_date?->toDateTimeString(),
            'approved_by' => $this->approved_by,
            'approved_date' => $this->approved_date?->toDateTimeString(),
            'fulfilled_date' => $this->fulfilled_date?->toDateTimeString(),
            'notes' => $this->notes,
            'rejection_reason' => $this->rejection_reason,
            
            // Relationships
            'item' => new InventoryItemResource($this->whenLoaded('item')),
            'requester' => $this->whenLoaded('requester', function () {
                return [
                    'id' => $this->requester->id,
                    'name' => $this->requester->name,
                    'email' => $this->requester->email,
                ];
            }),
            'approver' => $this->whenLoaded('approver', function () {
                return [
                    'id' => $this->approver->id,
                    'name' => $this->approver->name,
                    'email' => $this->approver->email,
                ];
            }),
            
            // Timestamps
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }
}
