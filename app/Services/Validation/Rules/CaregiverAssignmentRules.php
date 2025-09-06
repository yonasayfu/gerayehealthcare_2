<?php

namespace App\Services\Validation\Rules;

use App\Rules\StaffIsAvailableForShift;

class CaregiverAssignmentRules extends BaseResourceRules
{
    public static function store(): array
    {
        return [
            'patient_id' => 'required|exists:patients,id',
            'staff_id' => 'required|exists:staff,id',
            'status' => 'required|string|max:255',
            'shift_end' => 'required|date|after_or_equal:shift_start',
            'shift_start' => [
                'required',
                'date',
                new StaffIsAvailableForShift(request()->staff_id, request()->shift_end),
            ],
        ];
    }

    public static function update($item): array
    {
        return [
            'patient_id' => 'required|exists:patients,id',
            'staff_id' => 'required|exists:staff,id',
            'status' => 'required|string|max:255',
            'shift_end' => 'required|date|after_or_equal:shift_start',
            'shift_start' => [
                'required',
                'date',
                new StaffIsAvailableForShift(request()->staff_id, request()->shift_end, $item->id),
            ],
        ];
    }
}
