<?php

namespace App\Services\Validation\Rules;

class MedicalDocumentRules extends BaseResourceRules
{
    public static function store(): array
    {
        return [
            'patient_id' => ['required', 'integer', 'exists:patients,id'],
            'medical_visit_id' => ['nullable', 'integer', 'exists:medical_visits,id'],
            'document_type' => ['required', 'string', 'in:doctor_note,lab_request,lab_result,prescription,other'],
            'title' => ['nullable', 'string', 'max:255'],
            'document_date' => ['nullable', 'date'],
            // Accept an actual uploaded file; service will persist and set file_path
            'file' => ['nullable', 'file', 'mimes:pdf,doc,docx,jpg,jpeg,png', 'max:10240'],
            'file_path' => ['nullable', 'string', 'max:1024'],
            'summary' => ['nullable', 'string'],
            'is_printed' => ['sometimes', 'boolean'],
            'created_by_staff_id' => ['sometimes', 'integer', 'exists:staff,id'],
        ];
    }

    public static function update($model): array
    {
        return [
            'patient_id' => ['sometimes', 'integer', 'exists:patients,id'],
            'medical_visit_id' => ['nullable', 'integer', 'exists:medical_visits,id'],
            'document_type' => ['sometimes', 'string', 'in:doctor_note,lab_request,lab_result,prescription,other'],
            'title' => ['nullable', 'string', 'max:255'],
            'document_date' => ['nullable', 'date'],
            'file' => ['nullable', 'file', 'mimes:pdf,doc,docx,jpg,jpeg,png', 'max:10240'],
            'file_path' => ['nullable', 'string', 'max:1024'],
            'summary' => ['nullable', 'string'],
            'is_printed' => ['sometimes', 'boolean'],
            'created_by_staff_id' => ['sometimes', 'integer', 'exists:staff,id'],
        ];
    }
}
