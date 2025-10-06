<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PatientResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $contact = [];
        if (empty($this->emergency_contact_name) && is_string($this->emergency_contact)) {
            $decoded = json_decode($this->emergency_contact, true);
            if (is_array($decoded)) {
                $contact = $decoded;
            }
        }

        return [
            'id' => $this->id,
            'patient_code' => $this->patient_code,
            'fayda_id' => $this->fayda_id,
            'full_name' => $this->full_name,
            'date_of_birth' => $this->date_of_birth,
            'ethiopian_date_of_birth' => $this->ethiopian_date_of_birth,
            'gender' => $this->gender,
            'address' => $this->address,
            'city' => $this->city,
            'state' => $this->state,
            'country' => $this->country,
            'postal_code' => $this->postal_code,
            'phone_number' => $this->phone_number,
            'email' => $this->email,
            'emergency_contact' => $this->emergency_contact,
            'emergency_contact_name' => $this->emergency_contact_name ?? ($contact['name'] ?? null),
            'emergency_contact_phone' => $this->emergency_contact_phone ?? ($contact['phone'] ?? null),
            'emergency_contact_relationship' => $this->emergency_contact_relationship ?? ($contact['relationship'] ?? null),
            'medical_history' => $this->medical_history,
            'allergies' => $this->allergies,
            'current_medications' => $this->current_medications,
            'insurance_provider' => $this->insurance_provider,
            'insurance_policy_number' => $this->insurance_policy_number,
            'preferred_language' => $this->preferred_language,
            'blood_type' => $this->blood_type,
            'height' => $this->height,
            'weight' => $this->weight,
            'status' => $this->status,
            'notes' => $this->notes,
            'lead_source' => $this->source,
            'assigned_staff_id' => $this->registered_by_staff_id,
            'source' => $this->source,
            'geolocation' => $this->geolocation,
            'registered_by_staff_id' => $this->registered_by_staff_id,
            'registered_by_caregiver_id' => $this->registered_by_caregiver_id,
            'acquisition_source_id' => $this->acquisition_source_id,
            'marketing_campaign_id' => $this->marketing_campaign_id,
            'utm_campaign' => $this->utm_campaign,
            'utm_source' => $this->utm_source,
            'utm_medium' => $this->utm_medium,
            'lead_id' => $this->lead_id,
            'acquisition_cost' => $this->acquisition_cost,
            'acquisition_date' => $this->acquisition_date,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
