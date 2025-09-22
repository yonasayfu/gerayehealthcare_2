<?php

namespace App\Services\Messaging;

use App\Models\Message;
use App\Models\User;
use App\Models\MessageReaction;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class MessageService
{
    public function sendDirectMessage(User $recipient, array $data): Message
    {
        // data keys: message (nullable), attachment (UploadedFile|null), voice_message (UploadedFile|null), reply_to_id (int|null), duration (int|null)
        $attachmentPath = null;
        $attachmentFilename = null;
        $attachmentMimeType = null;
        $attachmentType = null;

        if (! empty($data['attachment'])) {
            $file = $data['attachment'];
            $attachmentFilename = $file->getClientOriginalName();
            $attachmentMimeType = $file->getClientMimeType();
            $attachmentPath = $file->store('messages/attachments', 'public');
            $attachmentType = $this->getAttachmentType($attachmentMimeType);
        }

        if (! empty($data['voice_message'])) {
            $file = $data['voice_message'];
            $attachmentFilename = $file->getClientOriginalName();
            $attachmentMimeType = $file->getClientMimeType();
            $attachmentPath = $file->store('messages/voice', 'public');
            $attachmentType = 'voice';
        }

        if (empty($data['message']) && ! $attachmentPath) {
            throw ValidationException::withMessages([
                'message' => 'Message text or attachment required',
            ]);
        }

        $messageData = [
            'sender_id' => Auth::id(),
            'receiver_id' => $recipient->id,
            'message' => $data['message'] ?? null,
            'attachment_path' => $attachmentPath,
            'attachment_filename' => $attachmentFilename,
            'attachment_mime_type' => $attachmentMimeType,
            'attachment_type' => $attachmentType,
        ];

        if (!empty($data['reply_to_id'])) {
            $messageData['reply_to_id'] = $data['reply_to_id'];
        }

        if (!empty($data['duration'])) {
            $messageData['duration'] = $data['duration'];
        }

        return Message::create($messageData);
    }

    public function deleteMessage(Message $message): void
    {
        // Remove per-message reactions first to avoid FK constraint issues
        if (method_exists($message, 'messageReactions')) {
            $message->messageReactions()->delete();
        }

        if ($message->attachment_path) {
            Storage::disk('public')->delete($message->attachment_path);
        }

        $message->delete();
    }

    public function pinMessage(Message $message): Message
    {
        $message->forceFill(['is_pinned' => true])->save();

        return $message->fresh();
    }

    public function unpinMessage(Message $message): Message
    {
        $message->forceFill(['is_pinned' => false])->save();

        return $message->fresh();
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

    public function addReaction(Message $message, User $user, string $emoji): ?MessageReaction
    {
        // Remove existing reaction from this user on this message with same emoji
        MessageReaction::where([
            'message_id' => $message->id,
            'user_id' => $user->id,
            'emoji' => $emoji,
        ])->delete();

        // Create new reaction
        return MessageReaction::create([
            'message_id' => $message->id,
            'user_id' => $user->id,
            'emoji' => $emoji,
        ]);
    }

    public function removeReaction(Message $message, User $user, string $emoji): void
    {
        MessageReaction::where([
            'message_id' => $message->id,
            'user_id' => $user->id,
            'emoji' => $emoji,
        ])->delete();
    }

    public function forwardMessage(Message $message, array $userIds): array
    {
        $forwardedMessages = [];
        
        foreach ($userIds as $userId) {
            $recipient = User::find($userId);
            if (!$recipient) continue;

            $data = ['message' => "Forwarded: " . ($message->message ?? 'Attachment')];
            
            // If original message has attachment, we could copy it here
            // For now, just forward the text content
            
            $forwardedMessages[] = $this->sendDirectMessage($recipient, $data);
        }
        
        return $forwardedMessages;
    }

    public function editMessage(Message $message, string $newContent): Message
    {
        $message->update([
            'message' => $newContent,
            'edited_at' => now(),
        ]);

        return $message->fresh();
    }

    private function getAttachmentType(string $mimeType): string
    {
        if (str_starts_with($mimeType, 'image/')) {
            return 'image';
        }
        
        if (str_starts_with($mimeType, 'video/')) {
            return 'video';
        }
        
        if (str_starts_with($mimeType, 'audio/')) {
            return 'audio';
        }
        
        return 'file';
    }
}
