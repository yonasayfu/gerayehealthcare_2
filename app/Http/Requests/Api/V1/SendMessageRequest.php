<?php

namespace App\Http\Requests\Api\V1;

use App\Services\Validation\Rules\MessageRules;
use Illuminate\Foundation\Http\FormRequest;

class SendMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return MessageRules::apiSendRules();
    }

    public function messages(): array
    {
        return MessageRules::messages();
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $errors = MessageRules::validateMessageContent($this->all());
            foreach ($errors as $error) {
                $validator->errors()->add('message', $error);
            }
        });
    }
}

