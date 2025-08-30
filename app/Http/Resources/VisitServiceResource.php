<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VisitServiceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
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
            'check_in_time' => optional($this->check_in_time)->toDateTimeString(),
            'check_out_time' => optional($this->check_out_time)->toDateTimeString(),
            'status' => $this->status,
            'visit_notes' => $this->visit_notes,
            'prescription_file_url' => $this->prescription_file_url,
            'vitals_file_url' => $this->vitals_file_url,
        ];
    }
}

