<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SendMessageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'message' => [
                'nullable',
                'string',
                'max:5000',
                'required_without:attachment',
            ],
            'attachment' => [
                'nullable',
                'file',
                'max:10240', // 10MB for API
                'mimes:jpg,jpeg,png,gif,pdf,doc,docx,txt,mp4,avi,mov,mp3,wav',
                'required_without:message',
            ],
            'reply_to_id' => [
                'nullable',
                'integer',
                'exists:messages,id',
            ],
            'priority' => [
                'nullable',
                'string',
                Rule::in(['low', 'normal', 'high', 'urgent']),
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'message.required_without' => 'Either message text or attachment is required.',
            'message.max' => 'Message cannot exceed 5000 characters.',
            'attachment.required_without' => 'Either message text or attachment is required.',
            'attachment.max' => 'Attachment cannot exceed 10MB.',
            'attachment.mimes' => 'Invalid file type. Allowed types: jpg, jpeg, png, gif, pdf, doc, docx, txt, mp4, avi, mov, mp3, wav.',
            'reply_to_id.exists' => 'The message you are replying to does not exist.',
            'priority.in' => 'Priority must be one of: low, normal, high, urgent.',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Validate reply_to_id belongs to the conversation with the target user
            if ($this->reply_to_id && $this->route('user')) {
                $replyMessage = \App\Models\Message::find($this->reply_to_id);
                $targetUserId = $this->route('user')->id;

                if ($replyMessage) {
                    $isInConversation = ($replyMessage->sender_id == auth()->id() && $replyMessage->receiver_id == $targetUserId) ||
                                      ($replyMessage->receiver_id == auth()->id() && $replyMessage->sender_id == $targetUserId);

                    if (! $isInConversation) {
                        $validator->errors()->add('reply_to_id', 'You can only reply to messages in this conversation.');
                    }
                }
            }
        });
    }
}
