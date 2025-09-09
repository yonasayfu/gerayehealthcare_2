<?php

namespace App\Services;

use App\Events\NewMessage;
use App\Exceptions\BusinessException;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class MessageService extends PerformanceOptimizedBaseService
{
    public function __construct(Message $message)
    {
        $this->model = Message::class;
        parent::__construct();
    }

    /**
     * Create a new message
     */
    public function create(array | object $data): Message
    {
        $data = is_object($data) ? (array) $data : $data;

        // Handle file upload if present
        if (isset($data['attachment']) && $data['attachment'] instanceof UploadedFile) {
            $data = $this->handleFileUpload($data);
        }

        // Validate message content
        if (empty($data['message']) && empty($data['attachment_path'])) {
            throw new BusinessException('Message content or attachment is required');
        }

        $message = $this->model->create($data);

        // Clear caches
        $this->clearConversationCaches($data['sender_id'], $data['receiver_id']);

        // Broadcast the new message event
        broadcast(new NewMessage($message->load(['sender', 'receiver', 'reactions', 'replyTo'])));

        return $message;
    }

    /**
     * Get conversations for a user
     */
    public function getConversations(int $userId, ?string $search = null): Collection
    {
        $cacheKey = "conversations_{$userId}" . ($search ? "_search_" . md5($search) : '');

        return $this->getCachedData($cacheKey, function () use ($userId, $search) {
            $query = User::where('id', '!=', $userId);

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")
                        ->orWhere('email', 'LIKE', "%{$search}%");
                });
            }

            return $query->get()->map(function ($user) use ($userId) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'profile_photo_path' => $user->profile_photo_path,
                    'last_message' => $this->getLastMessage($userId, $user->id),
                    'unread_count' => $this->getUnreadCount($userId, $user->id),
                ];
            });
        }, 300); // Cache for 5 minutes
    }

    /**
     * Get messages between two users
     */
    public function getConversationMessages(int $user1Id, int $user2Id, int $limit = 50): Collection
    {
        $cacheKey = "conversation_messages_{$user1Id}_{$user2Id}_{$limit}";

        return $this->getCachedData($cacheKey, function () use ($user1Id, $user2Id, $limit) {
            return Message::betweenUsers($user1Id, $user2Id)
                ->with(['sender', 'receiver'])
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get()
                ->reverse()
                ->values();
        }, 60); // Cache for 1 minute
    }

    /**
     * Get last message between two users
     */
    public function getLastMessage(int $user1Id, int $user2Id): ?array
    {
        $cacheKey = "last_message_{$user1Id}_{$user2Id}";

        return $this->getCachedData($cacheKey, function () use ($user1Id, $user2Id) {
            $message = Message::betweenUsers($user1Id, $user2Id)
                ->latest()
                ->first();

            if (!$message) {
                return null;
            }

            return [
                'id' => $message->id,
                'message' => $message->message,
                'created_at' => $message->created_at,
                'sender_name' => $message->sender->name,
                'has_attachment' => $message->hasAttachment(),
            ];
        }, 300); // Cache for 5 minutes
    }

    /**
     * Get unread messages count
     */
    public function getUnreadCount(int $userId, ?int $fromUserId = null): int
    {
        $cacheKey = "unread_count_{$userId}" . ($fromUserId ? "_{$fromUserId}" : '');

        return $this->getCachedData($cacheKey, function () use ($userId, $fromUserId) {
            $query = Message::where('receiver_id', $userId)->unread();

            if ($fromUserId) {
                $query->where('sender_id', $fromUserId);
            }

            return $query->count();
        }, 60); // Cache for 1 minute
    }

    /**
     * Mark conversation as read
     */
    public function markConversationAsRead(int $userId, int $fromUserId): void
    {
        Message::where('receiver_id', $userId)
            ->where('sender_id', $fromUserId)
            ->unread()
            ->update(['read_at' => now()]);

        // Clear caches
        $this->clearConversationCaches($userId, $fromUserId);
    }

    /**
     * Check if user can view message
     */
    public function canViewMessage(Message $message, int $userId): bool
    {
        return $message->sender_id === $userId || $message->receiver_id === $userId;
    }

    /**
     * Check if user can delete message
     */
    public function canDeleteMessage(Message $message, int $userId): bool
    {
        return $message->sender_id === $userId || $message->receiver_id === $userId;
    }

    /**
     * Handle file upload for message attachment
     */
    private function handleFileUpload(array $data): array
    {
        $file = $data['attachment'];

        // Validate file
        $maxSize = 10 * 1024 * 1024; // 10MB
        if ($file->getSize() > $maxSize) {
            throw new BusinessException('File size cannot exceed 10MB');
        }

        $allowedMimes = [
            'image/jpeg', 'image/png', 'image/gif', 'image/webp',
            'application/pdf', 'text/plain', 'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        ];

        if (!in_array($file->getMimeType(), $allowedMimes)) {
            throw new BusinessException('File type not allowed');
        }

        // Store file
        $path = $file->store('messages/attachments', 'public');

        $data['attachment_path'] = $path;
        $data['attachment_filename'] = $file->getClientOriginalName();
        $data['attachment_mime_type'] = $file->getMimeType();

        unset($data['attachment']);

        return $data;
    }

    /**
     * Clear conversation-related caches
     */
    private function clearConversationCaches(int $user1Id, int $user2Id): void
    {
        $this->forgetCache("conversations_{$user1Id}");
        $this->forgetCache("conversations_{$user2Id}");
        $this->forgetCache("conversation_messages_{$user1Id}_{$user2Id}_50");
        $this->forgetCache("conversation_messages_{$user2Id}_{$user1Id}_50");
        $this->forgetCache("last_message_{$user1Id}_{$user2Id}");
        $this->forgetCache("last_message_{$user2Id}_{$user1Id}");
        $this->forgetCache("unread_count_{$user1Id}");
        $this->forgetCache("unread_count_{$user2Id}");
        $this->forgetCache("unread_count_{$user1Id}_{$user2Id}");
        $this->forgetCache("unread_count_{$user2Id}_{$user1Id}");
    }

    /**
     * Delete message and clean up files
     */
    public function delete(int $id): ?bool
    {
        $message = $this->findById($id);

        // Delete attachment file if exists
        if ($message->attachment_path) {
            Storage::disk('public')->delete($message->attachment_path);
        }

        // Clear caches before deletion
        $this->clearConversationCaches($message->sender_id, $message->receiver_id);

        return parent::delete($id);
    }
}
