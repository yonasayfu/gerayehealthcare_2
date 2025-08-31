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
            'status' => 'required|string|max:255',
            'visit_notes' => 'nullable|string',
            'service_description' => 'nullable|string|max:500',
            'prescription_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'vitals_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ];
    }

    public static function update($item): array
    {
        return [
            'patient_id' => 'sometimes|exists:patients,id',
            'staff_id' => ['sometimes', 'exists:staff,id', new StaffIsAvailableForVisit],
            'scheduled_at' => 'sometimes|date|after:now',
            'status' => 'sometimes|string|max:255',
            'visit_notes' => 'sometimes|nullable|string',
            'service_description' => 'sometimes|nullable|string|max:500',
            'prescription_file' => 'sometimes|nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'vitals_file' => 'sometimes|nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ];
    }
}
