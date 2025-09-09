<?php

namespace App\Http\Requests\VisitService;

use Illuminate\Foundation\Http\FormRequest;

class CheckOutRequest extends FormRequest
{
    public function authorize(): bool
    {
        $visit = $this->route('visitService');

        return $this->user() && $this->user()->can('checkOut', $visit);
    }

    public function rules(): array
    {
        return [
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            // Optional client-reported timestamp
            'timestamp' => ['sometimes', 'date'],
        ];
    }
}
