<?php

namespace App\Services\Validation\Rules;

class MessageRules
{
    /**
     * Get validation rules for message creation
     */
    public static function createRules(): array
    {
        return [
            'receiver_id' => ['required', 'integer', 'exists:users,id'],
            'message' => ['nullable', 'string', 'max:5000'],
            'attachment' => ['nullable', 'file', 'max:20480', 'mimes:jpg,jpeg,png,pdf,doc,docx,txt'],
            'reply_to_id' => ['nullable', 'integer', 'exists:messages,id'],
            'priority' => ['nullable', 'string', 'in:low,normal,high,urgent'],
            'message_type' => ['nullable', 'string', 'in:text,file,image,system'],
        ];
    }

    /**
     * Get validation rules for API message sending
     */
    public static function apiSendRules(): array
    {
        return [
            'message' => ['nullable', 'string', 'max:5000'],
            'attachment' => ['nullable', 'file', 'max:20480', 'mimes:jpg,jpeg,png,pdf,doc,docx,txt'],
            'reply_to_id' => ['nullable', 'integer', 'exists:messages,id'],
            'priority' => ['nullable', 'string', 'in:low,normal,high,urgent'],
            'message_type' => ['nullable', 'string', 'in:text,file,image,system'],
        ];
    }

    /**
     * Get validation rules for message updates
     */
    public static function updateRules(): array
    {
        return [
            'message' => ['required', 'string', 'max:5000'],
        ];
    }

    /**
     * Get validation rules for group message creation
     */
    public static function groupMessageRules(): array
    {
        return [
            'group_id' => ['required', 'integer', 'exists:groups,id'],
            'message' => ['nullable', 'string', 'max:5000'],
            'attachment' => ['nullable', 'file', 'max:20480', 'mimes:jpg,jpeg,png,pdf,doc,docx,txt'],
            'reply_to_id' => ['nullable', 'integer', 'exists:messages,id'],
            'priority' => ['nullable', 'string', 'in:low,normal,high,urgent'],
            'message_type' => ['nullable', 'string', 'in:text,file,image,system'],
        ];
    }

    /**
     * Get custom validation messages
     */
    public static function messages(): array
    {
        return [
            'receiver_id.required' => 'Please select a recipient for your message.',
            'receiver_id.exists' => 'The selected recipient does not exist.',
            'message.max' => 'Message cannot exceed 5000 characters.',
            'attachment.max' => 'File size cannot exceed 20MB.',
            'attachment.mimes' => 'File must be a valid image, PDF, or document.',
            'reply_to_id.exists' => 'The message you are replying to does not exist.',
            'priority.in' => 'Priority must be one of: low, normal, high, urgent.',
            'message_type.in' => 'Message type must be one of: text, file, image, system.',
            'group_id.required' => 'Please select a group for your message.',
            'group_id.exists' => 'The selected group does not exist.',
        ];
    }

    /**
     * Validate that either message or attachment is provided
     */
    public static function validateMessageOrAttachment(array $data): bool
    {
        return !empty($data['message']) || !empty($data['attachment']);
    }

    /**
     * Get file upload validation rules
     */
    public static function fileUploadRules(): array
    {
        return [
            'attachment' => [
                'nullable',
                'file',
                'max:20480', // 20MB
                'mimes:jpg,jpeg,png,gif,pdf,doc,docx,txt,csv,xlsx,ppt,pptx',
            ],
        ];
    }

    /**
     * Get image upload validation rules
     */
    public static function imageUploadRules(): array
    {
        return [
            'attachment' => [
                'nullable',
                'image',
                'max:10240', // 10MB for images
                'mimes:jpg,jpeg,png,gif',
                'dimensions:max_width=4000,max_height=4000',
            ],
        ];
    }

    /**
     * Get document upload validation rules
     */
    public static function documentUploadRules(): array
    {
        return [
            'attachment' => [
                'nullable',
                'file',
                'max:20480', // 20MB
                'mimes:pdf,doc,docx,txt,csv,xlsx,ppt,pptx',
            ],
        ];
    }

    /**
     * Validate message content based on type
     */
    public static function validateMessageContent(array $data): array
    {
        $errors = [];

        // If no message and no attachment, it's invalid
        if (empty($data['message']) && empty($data['attachment'])) {
            $errors[] = 'Either message content or an attachment is required.';
        }

        // If message type is text but no message content
        if (($data['message_type'] ?? 'text') === 'text' && empty($data['message'])) {
            $errors[] = 'Text messages must have content.';
        }

        // If message type is file but no attachment
        if (in_array($data['message_type'] ?? 'text', ['file', 'image']) && empty($data['attachment'])) {
            $errors[] = 'File and image messages must have an attachment.';
        }

        return $errors;
    }

    /**
     * Get sanitization rules for message content
     */
    public static function sanitizeMessage(string $message): string
    {
        // Remove potentially harmful content
        $message = strip_tags($message);
        
        // Trim whitespace
        $message = trim($message);
        
        // Remove excessive line breaks
        $message = preg_replace('/\n{3,}/', "\n\n", $message);
        
        return $message;
    }

    /**
     * Check if user can send message to recipient
     */
    public static function canSendToRecipient($sender, $recipientId): bool
    {
        // Add your business logic here
        // For now, allow all authenticated users to send messages
        return $sender && $sender->id !== $recipientId;
    }

    /**
     * Get rate limiting rules
     */
    public static function getRateLimits(): array
    {
        return [
            'messages_per_minute' => 10,
            'messages_per_hour' => 100,
            'attachments_per_hour' => 20,
        ];
    }
}
