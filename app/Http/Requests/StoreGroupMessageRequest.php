<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreGroupMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'message' => ['nullable', 'string', 'max:5000'],
            'reply_to_id' => ['nullable', 'integer', 'exists:group_messages,id'],
            'attachment' => ['nullable', 'file', 'max:10240'],
            'priority' => ['nullable', 'in:low,normal,high'],
            'message_type' => ['nullable', 'in:text,file'],
        ];
    }
}
