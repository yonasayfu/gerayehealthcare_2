<?php

namespace App\Services\Messaging;

use App\Models\Message;
use App\Models\User;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class MessageService
{
    public function sendDirectMessage(User $recipient, array $data): Message
    {
        // data keys: message (nullable), attachment (UploadedFile|null)
        $attachmentPath = null;
        $attachmentFilename = null;
        $attachmentMimeType = null;

        if (! empty($data['attachment'])) {
            $file = $data['attachment'];
            $attachmentFilename = $file->getClientOriginalName();
            $attachmentMimeType = $file->getClientMimeType();
            $attachmentPath = $file->store('messages/attachments', 'public');
        }

        if (empty($data['message']) && ! $attachmentPath) {
            throw ValidationException::withMessages([
                'message' => 'Message text or attachment required',
            ]);
        }

        return Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $recipient->id,
            'message' => $data['message'] ?? null,
            'attachment_path' => $attachmentPath,
            'attachment_filename' => $attachmentFilename,
            'attachment_mime_type' => $attachmentMimeType,
        ]);
    }

    public function deleteMessage(Message $message): void
    {
        if ($message->attachment_path) {
            Storage::disk('public')->delete($message->attachment_path);
        }

        $message->delete();
    }

    /**
     * @return array{path:string, name:string, headers:array}
     *
     * @throws FileNotFoundException
     */
    public function prepareDownload(Message $message): array
    {
        if (! $message->attachment_path || ! Storage::disk('public')->exists($message->attachment_path)) {
            throw new FileNotFoundException('Attachment not found');
        }

        $name = $message->attachment_filename ?: basename($message->attachment_path);
        $headers = $message->attachment_mime_type ? ['Content-Type' => $message->attachment_mime_type] : [];

        return [
            'path' => $message->attachment_path,
            'name' => $name,
            'headers' => $headers,
        ];
    }
}
