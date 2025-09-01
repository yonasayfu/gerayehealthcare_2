<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LeadResource extends JsonResource
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
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'source' => $this->source,
            'status' => $this->status,
            'notes' => $this->notes,
            'interest_level' => $this->interest_level,
            'budget_range' => $this->budget_range,
            'timeline' => $this->timeline,
            'converted_to_patient' => $this->converted_to_patient,
            'converted_at' => $this->converted_at?->format('Y-m-d H:i:s'),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
            
            // Relationships
            'marketing_campaign' => new MarketingCampaignResource($this->whenLoaded('marketingCampaign')),
            'patient' => new PatientResource($this->whenLoaded('patient')),
            'assigned_to' => new UserResource($this->whenLoaded('assignedTo')),
        ];
    }
}
