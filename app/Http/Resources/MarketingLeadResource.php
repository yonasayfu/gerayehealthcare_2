<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MarketingLeadResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'lead_code' => $this->lead_code,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'country' => $this->country,
            'utm_source' => $this->utm_source,
            'utm_campaign' => $this->utm_campaign,
            'utm_medium' => $this->utm_medium,
            'lead_score' => $this->lead_score,
            'status' => $this->status,
            'conversion_date' => $this->conversion_date,
            'notes' => $this->notes,
            'source_campaign' => new MarketingCampaignResource($this->whenLoaded('sourceCampaign')),
            'landing_page' => new LandingPageResource($this->whenLoaded('landingPage')),
            'assigned_staff' => new StaffResource($this->whenLoaded('assignedStaff')),
            'converted_patient' => new PatientResource($this->whenLoaded('convertedPatient')),
        ];
    }
}
