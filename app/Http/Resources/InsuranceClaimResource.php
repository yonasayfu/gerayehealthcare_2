<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InsuranceClaimResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'claim_status' => $this->claim_status,
            'coverage_amount' => $this->coverage_amount,
            'paid_amount' => $this->paid_amount,
            'submitted_at' => $this->submitted_at,
            'processed_at' => $this->processed_at,
            'payment_due_date' => $this->payment_due_date,
            'payment_received_at' => $this->payment_received_at,
            'insurance_company' => $this->whenLoaded('insuranceCompany', fn() => ['id' => $this->insuranceCompany->id, 'name' => $this->insuranceCompany->name]),
            'policy' => $this->whenLoaded('policy', fn() => ['id' => $this->policy->id, 'service_type' => $this->policy->service_type]),
            'invoice' => $this->whenLoaded('invoice', fn() => ['id' => $this->invoice->id, 'invoice_number' => $this->invoice->invoice_number]),
        ];
    }
}

