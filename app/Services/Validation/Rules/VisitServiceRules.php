<?php

namespace App\Services\Validation\Rules;

use App\Rules\StaffIsAvailableForVisit;

class VisitServiceRules extends BaseResourceRules
{
    public static function store(): array
    {
        return [
            'patient_id' => 'required|exists:patients,id',
            'staff_id' => ['required', 'exists:staff,id', new StaffIsAvailableForVisit],
            'scheduled_at' => 'required|date|after:now',
            'check_in_time' => 'nullable|date',
            'check_out_time' => 'nullable|date|after_or_equal:check_in_time',
            'status' => 'required|string|max:255',
            'visit_notes' => 'nullable|string',
            'service_description' => 'nullable|string|max:500',
            'prescription_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'vitals_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            // Optional manual geo input (admin web)
            'check_in_latitude' => 'nullable|numeric|between:-90,90',
            'check_in_longitude' => 'nullable|numeric|between:-180,180',
            'check_out_latitude' => 'nullable|numeric|between:-90,90',
            'check_out_longitude' => 'nullable|numeric|between:-180,180',
        ];
    }

    public static function update($item): array
    {
        return [
            'patient_id' => 'sometimes|exists:patients,id',
            'staff_id' => ['sometimes', 'exists:staff,id', new StaffIsAvailableForVisit],
            'scheduled_at' => 'sometimes|date|after:now',
            'check_in_time' => 'sometimes|nullable|date',
            'check_out_time' => 'sometimes|nullable|date|after_or_equal:check_in_time',
            'status' => 'sometimes|string|max:255',
            'visit_notes' => 'sometimes|nullable|string',
            'service_description' => 'sometimes|nullable|string|max:500',
            'prescription_file' => 'sometimes|nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'vitals_file' => 'sometimes|nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            // Optional manual geo input (admin web)
            'check_in_latitude' => 'sometimes|nullable|numeric|between:-90,90',
            'check_in_longitude' => 'sometimes|nullable|numeric|between:-180,180',
            'check_out_latitude' => 'sometimes|nullable|numeric|between:-90,90',
            'check_out_longitude' => 'sometimes|nullable|numeric|between:-180,180',
        ];
    }
}
