<?php

namespace App\Services\Validation\Rules;

class PrescriptionRules extends BaseResourceRules
{
    public static function store(): array
    {
        return [
            'patient_id' => ['required', 'integer', 'exists:patients,id'],
            'medical_document_id' => ['nullable', 'integer', 'exists:medical_documents,id'],
            'prescribed_date' => ['required', 'date'],
            'status' => ['required', 'string', 'in:draft,final,dispensed,cancelled'],
            'instructions' => ['nullable', 'string'],
            'created_by_staff_id' => ['required', 'integer', 'exists:staff,id'],
            // Items (optional on create)
            'items' => ['sometimes', 'array'],
            'items.*.medication_name' => ['required_with:items', 'string', 'max:255'],
            'items.*.dosage' => ['nullable', 'string', 'max:255'],
            'items.*.frequency' => ['nullable', 'string', 'max:255'],
            'items.*.duration' => ['nullable', 'string', 'max:255'],
            'items.*.notes' => ['nullable', 'string'],
        ];
    }

    public static function update($model): array
    {
        return [
            'patient_id' => ['sometimes', 'integer', 'exists:patients,id'],
            'medical_document_id' => ['nullable', 'integer', 'exists:medical_documents,id'],
            'prescribed_date' => ['sometimes', 'date'],
            'status' => ['sometimes', 'string', 'in:draft,final,dispensed,cancelled'],
            'instructions' => ['nullable', 'string'],
            'created_by_staff_id' => ['sometimes', 'integer', 'exists:staff,id'],
            // Items updates can be handled by dedicated endpoints later; allow partial payloads
            'items' => ['sometimes', 'array'],
            'items.*.id' => ['sometimes', 'integer', 'exists:prescription_items,id'],
            'items.*.medication_name' => ['sometimes', 'string', 'max:255'],
            'items.*.dosage' => ['nullable', 'string', 'max:255'],
            'items.*.frequency' => ['nullable', 'string', 'max:255'],
            'items.*.duration' => ['nullable', 'string', 'max:255'],
            'items.*.notes' => ['nullable', 'string'],
        ];
    }
}
