<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'invoice_number' => $this->invoice_number,
            'invoice_date' => optional($this->invoice_date)->toDateString(),
            'due_date' => optional($this->due_date)->toDateString(),
            'status' => $this->status,
            'grand_total' => $this->grand_total,
            'paid_at' => optional($this->paid_at)->toDateTimeString(),
            'patient' => $this->whenLoaded('patient', fn() => ['id' => $this->patient->id, 'full_name' => $this->patient->full_name]),
            'insurance_company' => $this->whenLoaded('insuranceCompany', fn() => ['id' => $this->insuranceCompany->id, 'name' => $this->insuranceCompany->name]),
            'items' => $this->whenLoaded('items', function () {
                return $this->items->map(fn($i) => [
                    'id' => $i->id,
                    'description' => $i->description ?? null,
                    'quantity' => $i->quantity ?? 1,
                    'unit_price' => $i->unit_price ?? null,
                    'total' => $i->total ?? null,
                ]);
            }),
        ];
    }
}

