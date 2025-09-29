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
            'service_type' => $this->service_type ?? 'General',
            'priority' => $this->priority ?? 'Normal',
            'visit_location' => $this->visit_location ?? null,
            'patient_location' => $this->patient_location ?? null,
            'gps_coordinates' => $this->gps_coordinates ?? null,
            'check_in_time' => optional($this->check_in_time)->toDateTimeString(),
            'check_out_time' => optional($this->check_out_time)->toDateTimeString(),
            'check_in_latitude' => $this->check_in_latitude,
            'check_in_longitude' => $this->check_in_longitude,
            'check_out_latitude' => $this->check_out_latitude,
            'check_out_longitude' => $this->check_out_longitude,
            'check_in_location' => null,
            'check_out_location' => null,
            'status' => $this->status,
            'visit_notes' => $this->visit_notes,
            'prescription_file_url' => $this->prescription_file_url,
            'vitals_file_url' => $this->vitals_file_url,
            'created_at' => optional($this->created_at)->toDateTimeString(),
            'updated_at' => optional($this->updated_at)->toDateTimeString(),
        ];
    }
}
