<?php

namespace App\DTOs;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;

class CreateMessageDTO extends BaseDTO
{
    public int $sender_id;

    public int $receiver_id;

    public ?string $message = null;

    public ?UploadedFile $attachment = null;

    public string $priority = 'normal';

    public ?int $parent_id = null;

    /**
     * Create DTO from HTTP request
     */
    public static function fromRequest(Request $request): self
    {
        $dto = self::create();
        $dto->populate([
            'sender_id' => Auth::id(),
            'receiver_id' => $request->input('receiver_id'),
            'message' => $request->input('message'),
            'attachment' => $request->file('attachment'),
            'priority' => $request->input('priority', 'normal'),
            'parent_id' => $request->input('parent_id'),
        ]);

        $dto->validate();

        return $dto;
    }

    /**
     * Validation rules
     */
    protected function rules(): array
    {
        return [
            'sender_id' => 'required|exists:users,id',
            'receiver_id' => 'required|exists:users,id|different:sender_id',
            'message' => 'nullable|string|max:5000',
            'attachment' => [
                'nullable',
                'file',
                'max:10240', // 10MB
                'mimes:jpeg,png,gif,webp,pdf,txt,doc,docx',
            ],
            'priority' => 'in:low,normal,high,urgent',
            'parent_id' => 'nullable|exists:messages,id',
        ];
    }

    /**
     * Custom validation messages
     */
    protected function messages(): array
    {
        return [
            'receiver_id.required' => 'Please select a recipient',
            'receiver_id.exists' => 'Selected recipient does not exist',
            'receiver_id.different' => 'You cannot send a message to yourself',
            'message.max' => 'Message cannot exceed 5000 characters',
            'attachment.max' => 'File size cannot exceed 10MB',
            'attachment.mimes' => 'File type not supported. Allowed: images, PDF, text, Word documents',
            'priority.in' => 'Invalid priority level',
            'parent_id.exists' => 'Parent message does not exist',
        ];
    }

    /**
     * Custom validation
     */
    protected function customValidation(): void
    {
        // Either message or attachment must be provided
        if (empty($this->message) && ! $this->attachment) {
            $this->addError('message', 'Either message content or attachment is required');
        }

        // Validate parent message belongs to conversation
        if ($this->parent_id) {
            $parentMessage = \App\Models\Message::find($this->parent_id);
            if ($parentMessage) {
                $isInConversation = ($parentMessage->sender_id === $this->sender_id && $parentMessage->receiver_id === $this->receiver_id) ||
                                  ($parentMessage->sender_id === $this->receiver_id && $parentMessage->receiver_id === $this->sender_id);

                if (! $isInConversation) {
                    $this->addError('parent_id', 'Parent message is not part of this conversation');
                }
            }
        }
    }

    /**
     * Transform data before validation
     */
    protected function transform(): void
    {
        // Trim message content
        if ($this->message) {
            $this->message = trim($this->message);
            if (empty($this->message)) {
                $this->message = null;
            }
        }

        // Ensure priority is lowercase
        if ($this->priority) {
            $this->priority = strtolower($this->priority);
        }
    }
}
