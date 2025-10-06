<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MarketingLeadResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        $fullName = trim(($this->first_name ?? '').' '.($this->last_name ?? ''));
        $converted = !is_null($this->converted_patient_id);

        return [
            'id' => $this->id,
            'lead_code' => $this->lead_code,
            'source_campaign_id' => $this->source_campaign_id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'full_name' => $fullName,
            'email' => $this->email,
            'phone' => $this->phone,
            'country' => $this->country,
            'utm_source' => $this->utm_source,
            'utm_campaign' => $this->utm_campaign,
            'utm_medium' => $this->utm_medium,
            'lead_score' => $this->lead_score !== null ? (int) $this->lead_score : null,
            'status' => $this->status,
            'notes' => $this->notes,
            'assigned_staff_id' => $this->assigned_staff_id,
            'converted_patient_id' => $this->converted_patient_id,
            'converted_to_patient' => $converted,
            'converted_at' => $this->conversion_date?->toDateTimeString(),
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
            'source' => $this->utm_source ?? $this->sourceCampaign?->campaign_name,
            'source_campaign' => new MarketingCampaignResource($this->whenLoaded('sourceCampaign')),
            'landing_page' => new LandingPageResource($this->whenLoaded('landingPage')),
            'assigned_staff' => new StaffResource($this->whenLoaded('assignedStaff')),
            'converted_patient' => new PatientResource($this->whenLoaded('convertedPatient')),
        ];
    }
}
