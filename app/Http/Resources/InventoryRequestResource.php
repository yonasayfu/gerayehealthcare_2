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
            'item_id' => $this->item_id,
            'requester_id' => $this->requester_id,
            'approver_id' => $this->approver_id,
            'quantity_requested' => $this->quantity_requested,
            'quantity_approved' => $this->quantity_approved,
            'reason' => $this->reason,
            'priority' => $this->priority,
            'status' => $this->status,
            'needed_by_date' => $this->needed_by_date?->toDateString(),
            'approved_at' => $this->approved_at?->toDateTimeString(),
            'fulfilled_at' => $this->fulfilled_at?->toDateTimeString(),
            'item' => $this->whenLoaded('item', fn () => new InventoryItemResource($this->item)),
            'requester' => $this->whenLoaded('requester', function () {
                return [
                    'id' => $this->requester->id,
                    'first_name' => $this->requester->first_name,
                    'last_name' => $this->requester->last_name,
                    'email' => $this->requester->user?->email,
                ];
            }),
            'approver' => $this->whenLoaded('approver', function () {
                return [
                    'id' => $this->approver->id,
                    'first_name' => $this->approver->first_name,
                    'last_name' => $this->approver->last_name,
                    'email' => $this->approver->user?->email,
                ];
            }),
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }
}
