<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class GroupMessage extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'group_id',
        'sender_id',
        'message',
        'reply_to_id',
        'attachment_path',
        'attachment_filename',
        'attachment_mime_type',
        'message_type',
        'is_pinned',
    ];

    protected $casts = [
        'is_pinned' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $appends = ['attachment_url'];

    /**
     * Message types
     */
    const TYPE_TEXT = 'text';

    const TYPE_FILE = 'file';

    const TYPE_IMAGE = 'image';

    const TYPE_SYSTEM = 'system';

    /**
     * Get the group this message belongs to
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    /**
     * Get the sender of the message
     */
    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Get the message this is replying to
     */
    public function replyTo(): BelongsTo
    {
        return $this->belongsTo(GroupMessage::class, 'reply_to_id');
    }

    /**
     * Get reactions to this message
     */
    public function reactions(): MorphMany
    {
        return $this->morphMany(Reaction::class, 'reactable');
    }

    /**
     * Get attachment URL
     */
    public function getAttachmentUrlAttribute(): ?string
    {
        if (! $this->attachment_path) {
            return null;
        }

        return Storage::url($this->attachment_path);
    }

    /**
     * Check if message has attachment
     */
    public function hasAttachment(): bool
    {
        return ! is_null($this->attachment_path);
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
     * Scope for pinned messages
     */
    public function scopePinned($query)
    {
        return $query->where('is_pinned', true);
    }

    /**
     * Scope for messages in a specific group
     */
    public function scopeInGroup($query, int $groupId)
    {
        return $query->where('group_id', $groupId);
    }

    /**
     * Pin this message
     */
    public function pin(): void
    {
        $this->update(['is_pinned' => true]);
    }

    /**
     * Unpin this message
     */
    public function unpin(): void
    {
        $this->update(['is_pinned' => false]);
    }
}
