<?php

namespace App\Services\Validation\Rules;

class EmployeeInsuranceRecordRules extends BaseResourceRules
{
    public static function store(): array
    {
        return [
            'staff_id' => 'required|exists:staff,id',
            'insurance_company_id' => 'required|exists:insurance_companies,id',
            'policy_number' => 'required|string|max:255|unique:employee_insurance_records,policy_number',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'coverage_amount' => 'required|numeric|min:0',
            'premium_amount' => 'required|numeric|min:0',
            'status' => 'required|string|in:Active,Expired,Cancelled',
        ];
    }
    
    public static function update($record): array
    {
        return [
            'staff_id' => 'required|exists:staff,id',
            'insurance_company_id' => 'required|exists:insurance_companies,id',
            'policy_number' => 'required|string|max:255|unique:employee_insurance_records,policy_number,' . $record->id,
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'coverage_amount' => 'required|numeric|min:0',
            'premium_amount' => 'required|numeric|min:0',
            'status' => 'required|string|in:Active,Expired,Cancelled',
        ];
    }
}
