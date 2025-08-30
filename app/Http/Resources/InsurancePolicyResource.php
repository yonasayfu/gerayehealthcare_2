<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InsurancePolicyResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'service_type' => $this->service_type,
            'coverage_percentage' => $this->coverage_percentage,
            'coverage_type' => $this->coverage_type,
            'is_active' => (bool) $this->is_active,
            'insurance_company' => $this->whenLoaded('insuranceCompany', fn() => ['id' => $this->insuranceCompany->id, 'name' => $this->insuranceCompany->name]),
            'corporate_client' => $this->whenLoaded('corporateClient', fn() => ['id' => $this->corporateClient->id, 'organization_name' => $this->corporateClient->organization_name]),
        ];
    }
}

