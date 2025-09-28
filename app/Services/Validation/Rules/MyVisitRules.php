<?php

namespace App\Services\Validation\Rules;

class MyVisitRules extends BaseResourceRules
{
    public static function store(): array
    {
        return [
            'patient_id' => 'required|exists:patients,id',
            'service_id' => 'required|exists:services,id',
            'staff_id' => 'required|exists:staff,id',
            'visit_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'status' => 'required|string|in:Scheduled,In Progress,Completed,Cancelled',
            'notes' => 'nullable|string',
        ];
    }

    public static function update($visit): array
    {
        return [
            'patient_id' => 'required|exists:patients,id',
            'service_id' => 'required|exists:services,id',
            'staff_id' => 'required|exists:staff,id',
            'visit_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'status' => 'required|string|in:Scheduled,In Progress,Completed,Cancelled',
            'notes' => 'nullable|string',
        ];
    }
}
