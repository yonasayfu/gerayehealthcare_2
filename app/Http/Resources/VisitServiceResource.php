<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

class VisitServiceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $scheduledAt = $this->scheduled_at ? Carbon::parse($this->scheduled_at) : null;
        $checkIn = $this->check_in_time ? Carbon::parse($this->check_in_time) : null;
        $checkOut = $this->check_out_time ? Carbon::parse($this->check_out_time) : null;
        $actualDuration = ($checkIn && $checkOut) ? $checkIn->diffInMinutes($checkOut, false) : null;
        if (is_int($actualDuration) && $actualDuration < 0) {
            $actualDuration = 0;
        }

        return [
            'id' => $this->id,
            'patient_id' => $this->patient_id,
            'assigned_staff_id' => $this->staff_id,
            'patient' => $this->whenLoaded('patient', function () {
                return [
                    'id' => $this->patient->id,
                    'full_name' => $this->patient->full_name,
                ];
            }),
            'staff' => $this->whenLoaded('staff', function () {
                return [
                    'id' => $this->staff->id,
                    'full_name' => $this->staff->full_name,
                ];
            }),
            'scheduled_at' => optional($this->scheduled_at)->toDateTimeString(),
            // Fields expected by mobile VisitServiceModel
            'visit_date' => $scheduledAt?->toDateString(),
            'scheduled_start_time' => $scheduledAt?->toTimeString(),
            'scheduled_end_time' => null,
            'actual_start_time' => $checkIn?->toTimeString(),
            'actual_end_time' => $checkOut?->toTimeString(),
            'actual_duration' => $actualDuration,
            'service_type' => $this->service_type ?? 'general',
            'priority' => $this->priority ?? 'normal',
            'visit_location' => $this->visit_location,
            'patient_location' => $this->patient_location,
            'gps_coordinates' => $this->gps_coordinates,
            'check_in_time' => optional($this->check_in_time)->toDateTimeString(),
            'check_out_time' => optional($this->check_out_time)->toDateTimeString(),
            'check_in_latitude' => $this->check_in_latitude,
            'check_in_longitude' => $this->check_in_longitude,
            'check_out_latitude' => $this->check_out_latitude,
            'check_out_longitude' => $this->check_out_longitude,
            'check_in_location' => $this->check_in_location,
            'check_out_location' => $this->check_out_location,
            'status' => $this->status,
            'visit_notes' => $this->visit_notes,
            'service_notes' => $this->visit_notes,
            'patient_condition' => $this->patient_condition,
            'treatment_provided' => $this->treatment_provided,
            'medications_administered' => $this->medications_administered,
            'follow_up_required' => (bool) $this->follow_up_required,
            'follow_up_date' => optional($this->follow_up_date)->toDateString(),
            'follow_up_notes' => $this->follow_up_notes,
            'prescription_file_url' => $this->prescription_file_url,
            'vitals_file_url' => $this->vitals_file_url,
            'total_cost' => $this->cost,
            'paid_amount' => null,
            'payment_status' => $this->payment_status,
            'payment_method' => $this->payment_method,
            'insurance_claim_id' => $this->insurance_claim_id,
            'rating' => $this->rating,
            'feedback' => $this->feedback,
            'cancellation_reason' => $this->cancellation_reason,
            'cancelled_at' => optional($this->cancelled_at)->toDateTimeString(),
            'cancelled_by' => $this->cancelled_by,
            'created_at' => optional($this->created_at)->toDateTimeString(),
            'updated_at' => optional($this->updated_at)->toDateTimeString(),
        ];
    }
}
