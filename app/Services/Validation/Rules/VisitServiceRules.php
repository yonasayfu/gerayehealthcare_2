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
            'scheduled_at' => 'required|date',
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
            'patient_id' => 'required|exists:patients,id',
            'staff_id' => ['required', 'exists:staff,id', new StaffIsAvailableForVisit($item->id)],
            'scheduled_at' => 'required|date',
            'status' => 'required|string|max:255',
            'visit_notes' => 'nullable|string',
            'service_description' => 'nullable|string|max:500',
            'prescription_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'vitals_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ];
    }
}