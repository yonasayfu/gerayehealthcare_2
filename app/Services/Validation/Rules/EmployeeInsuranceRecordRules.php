<?php

namespace App\Services\Validation\Rules;

class EmployeeInsuranceRecordRules
{
    public static function store(): array
    {
        return [
            'patient_id' => 'required|integer|exists:patients,id',
            'policy_id' => 'required|integer|exists:insurance_policies,id',
            'kebele_id' => 'nullable|string|max:50',
            'woreda' => 'nullable|string|max:100',
            'region' => 'nullable|string|max:100',
            'federal_id' => 'nullable|string|max:50',
            'ministry_department' => 'nullable|string|max:255',
            'employee_id_number' => 'nullable|string|max:100',
            'verified' => 'boolean',
            'verified_at' => 'nullable|date',
        ];
    }

    public static function update(): array
    {
        return [
            'patient_id' => 'sometimes|required|integer|exists:patients,id',
            'policy_id' => 'sometimes|required|integer|exists:insurance_policies,id',
            'kebele_id' => 'nullable|string|max:50',
            'woreda' => 'nullable|string|max:100',
            'region' => 'nullable|string|max:100',
            'federal_id' => 'nullable|string|max:50',
            'ministry_department' => 'nullable|string|max:255',
            'employee_id_number' => 'nullable|string|max:100',
            'verified' => 'boolean',
            'verified_at' => 'nullable|date',
        ];
    }
}
