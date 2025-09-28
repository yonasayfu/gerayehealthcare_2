<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVisitReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Controller checks ownership; allow here.
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'service_id' => ['required', 'integer', 'exists:services,id'],
            'visit_notes' => ['nullable', 'string', 'max:5000'],
            'prescription_file' => ['sometimes', 'file', 'max:10240'],
            'vitals_file' => ['sometimes', 'file', 'max:10240'],
            // allow optional cost override for staff flow if provided
            'cost' => ['sometimes', 'numeric', 'min:0'],
        ];
    }
}

