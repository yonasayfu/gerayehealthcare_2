<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class UpdateVisitServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'scheduled_at' => ['sometimes', 'date'],
            'service_description' => ['sometimes', 'string', 'max:1000'],
        ];
    }
}
