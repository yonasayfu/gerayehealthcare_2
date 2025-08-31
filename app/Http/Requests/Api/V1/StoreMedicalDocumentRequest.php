<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class StoreMedicalDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->staff; // staff-only upload
    }

    public function rules(): array
    {
        return [
            'patient_id' => ['required', 'integer', 'exists:patients,id'],
            'document_type' => ['required', 'string', 'max:255'],
            'title' => ['required', 'string', 'max:255'],
            'document_date' => ['required', 'date'],
            'summary' => ['nullable', 'string', 'max:5000'],
            'file' => ['required', 'file', 'max:20480'],
        ];
    }
}

