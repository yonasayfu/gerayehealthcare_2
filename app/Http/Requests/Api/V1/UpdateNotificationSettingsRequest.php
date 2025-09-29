<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class UpdateNotificationSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'enable_push' => ['sometimes', 'boolean'],
            'enable_email' => ['sometimes', 'boolean'],
            'enable_sms' => ['sometimes', 'boolean'],
            'channels' => ['sometimes', 'array'],
            'channels.*' => ['string'],
            'enable_notifications' => ['sometimes', 'boolean'],
        ];
    }
}
