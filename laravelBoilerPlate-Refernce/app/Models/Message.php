<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Message extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'sender_id',
        'receiver_id',
        'message',
        'attachment_path',
        'attachment_filename',
        'attachment_mime_type',
        'read_at',
        'message_type',
        'priority',
        'parent_id',
        'reply_to_id',
    ];

    protected $casts = [
        'read_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Message types
     */
    const TYPE_TEXT = 'text';
    const TYPE_FILE = 'file';
    const TYPE_IMAGE = 'image';
    const TYPE_SYSTEM = 'system';

    /**
     * Priority levels
     */
    const PRIORITY_LOW = 'low';
    const PRIORITY_NORMAL = 'normal';
    const PRIORITY_HIGH = 'high';
    const PRIORITY_URGENT = 'urgent';

    /**
     * Get the sender of the message
     */
    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Get the receiver of the message
     */
    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    /**
     * Get the parent message (for replies)
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Message::class, 'parent_id');
    }

    /**
     * Get replies to this message
     */
    public function replies()
    {
        return $this->hasMany(Message::class, 'parent_id');
    }

    /**
     * Get the message this is replying to
     */
    public function replyTo(): BelongsTo
    {
        return $this->belongsTo(Message::class, 'reply_to_id');
    }

    /**
     * Get reactions to this message
     */
    public function reactions(): MorphMany
    {
        return $this->morphMany(Reaction::class, 'reactable');
    }

    /**
     * Scope for unread messages
     */
    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    /**
     * Scope for read messages
     */
    public function scopeRead($query)
    {
        return $query->whereNotNull('read_at');
    }

    /**
     * Scope for messages between two users
     */
    public function scopeBetweenUsers($query, $user1Id, $user2Id)
    {
        return $query->where(function ($q) use ($user1Id, $user2Id) {
            $q->where('sender_id', $user1Id)->where('receiver_id', $user2Id);
        })->orWhere(function ($q) use ($user1Id, $user2Id) {
            $q->where('sender_id', $user2Id)->where('receiver_id', $user1Id);
        });
    }

    /**
     * Scope for messages by priority
     */
    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    /**
     * Check if message is read
     */
    public function isRead(): bool
    {
        return !is_null($this->read_at);
    }

    /**
     * Check if message has attachment
     */
    public function hasAttachment(): bool
    {
        return !is_null($this->attachment_path);
    }

    /**
     * Get attachment URL
     */
    public function getAttachmentUrlAttribute(): ?string
    {
        if (!$this->attachment_path) {
            return null;
        }

        return asset('storage/' . $this->attachment_path);
    }

    /**
     * Get message type based on content
     */
    public function getMessageTypeAttribute(): string
    {
        if ($this->hasAttachment()) {
            $mimeType = $this->attachment_mime_type;
            if (str_starts_with($mimeType, 'image/')) {
                return self::TYPE_IMAGE;
            }
            return self::TYPE_FILE;
        }

        return self::TYPE_TEXT;
    }

    /**
     * Get formatted file size
     */
    public function getFormattedFileSizeAttribute(): ?string
    {
        if (!$this->attachment_path || !file_exists(storage_path('app/public/' . $this->attachment_path))) {
            return null;
        }

        $bytes = filesize(storage_path('app/public/' . $this->attachment_path));
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Mark message as read
     */
    public function markAsRead(): void
    {
        if (!$this->isRead()) {
            $this->update(['read_at' => now()]);
        }
    }

    /**
     * Mark message as unread
     */
    public function markAsUnread(): void
    {
        if ($this->isRead()) {
            $this->update(['read_at' => null]);
        }
    }
}
