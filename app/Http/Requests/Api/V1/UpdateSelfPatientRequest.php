<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSelfPatientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null; // Actual ownership enforced in controller lookup
    }

    public function rules(): array
    {
        return [
            'full_name' => ['sometimes', 'string', 'max:255'],
            'phone_number' => ['sometimes', 'string', 'max:255'],
            'address' => ['sometimes', 'string', 'max:1000'],
        ];
    }
}
