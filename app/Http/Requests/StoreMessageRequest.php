<?php

namespace App\Http\Requests;

use App\Services\Messaging\TelegramInboxService;
use App\Services\Validation\Rules\MessageRules;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        $context = $this->input('context', TelegramInboxService::CONTEXT_DIRECT);

        if ($context === TelegramInboxService::CONTEXT_CHANNEL) {
            return array_merge(
                [
                    'context' => ['required', Rule::in([TelegramInboxService::CONTEXT_DIRECT, TelegramInboxService::CONTEXT_CHANNEL])],
                ],
                MessageRules::groupMessageRules()
            );
        }

        return array_merge(
            [
                'context' => ['nullable', Rule::in([TelegramInboxService::CONTEXT_DIRECT, TelegramInboxService::CONTEXT_CHANNEL])],
            ],
            MessageRules::createRules()
        );
    }

    public function messages(): array
    {
        return MessageRules::messages();
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Include file presence when running extra content validation
            $payload = $this->all();
            if (! array_key_exists('attachment', $payload)) {
                $payload['attachment'] = $this->hasFile('attachment') ? '1' : null;
            }
            $errors = MessageRules::validateMessageContent($payload);
            foreach ($errors as $error) {
                $validator->errors()->add('message', $error);
            }
        });
    }
}
