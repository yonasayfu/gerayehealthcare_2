<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMessageRequest extends FormRequest
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
            'receiver_id' => [
                'required',
                'integer',
                'exists:users,id',
                'different:' . auth()->id(), // Cannot send message to self
            ],
            'message' => [
                'nullable',
                'string',
                'max:5000',
                'required_without:attachment', // Message or attachment required
            ],
            'attachment' => [
                'nullable',
                'file',
                'max:20480', // 20MB max
                'mimes:jpg,jpeg,png,gif,pdf,doc,docx,txt,zip,rar,mp4,avi,mov,mp3,wav',
                'required_without:message', // Message or attachment required
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
            'receiver_id.required' => 'Please select a recipient for the message.',
            'receiver_id.exists' => 'The selected recipient does not exist.',
            'receiver_id.different' => 'You cannot send a message to yourself.',
            'message.required_without' => 'Please provide either a message or an attachment.',
            'message.max' => 'The message cannot exceed 5000 characters.',
            'attachment.required_without' => 'Please provide either a message or an attachment.',
            'attachment.max' => 'The attachment cannot exceed 20MB.',
            'attachment.mimes' => 'The attachment must be a valid file type (jpg, jpeg, png, gif, pdf, doc, docx, txt, zip, rar, mp4, avi, mov, mp3, wav).',
            'reply_to_id.exists' => 'The message you are replying to does not exist.',
            'priority.in' => 'The priority must be one of: low, normal, high, urgent.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'receiver_id' => 'recipient',
            'reply_to_id' => 'reply message',
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Additional validation: Check if replying to a message in the same conversation
            if ($this->reply_to_id && $this->receiver_id) {
                $replyMessage = \App\Models\Message::find($this->reply_to_id);
                if ($replyMessage) {
                    $isInConversation = ($replyMessage->sender_id == auth()->id() && $replyMessage->receiver_id == $this->receiver_id) ||
                                      ($replyMessage->receiver_id == auth()->id() && $replyMessage->sender_id == $this->receiver_id);
                    
                    if (!$isInConversation) {
                        $validator->errors()->add('reply_to_id', 'You can only reply to messages in the same conversation.');
                    }
                }
            }
        });
    }
}
