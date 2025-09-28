<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Storage; // Import Storage facade

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'sender_id',
        'receiver_id',
        'message',
        'reply_to_id',
        'read_at',
        'attachment_path',
        'attachment_filename',
        'attachment_mime_type',
        'attachment_type',
        'duration',
        'edited_at',
        'deleted_for_sender_at',
        'deleted_for_receiver_at',
        'is_pinned',
    ];

    protected $casts = [
        'read_at' => 'datetime',
        'edited_at' => 'datetime',
        'deleted_for_sender_at' => 'datetime',
        'deleted_for_receiver_at' => 'datetime',
        'is_pinned' => 'boolean',
        'duration' => 'integer',
    ];

    // Add appends for attachment URL
    protected $appends = ['attachment_url'];

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public function replyTo(): BelongsTo
    {
        return $this->belongsTo(Message::class, 'reply_to_id');
    }

    public function messageReactions(): HasMany
    {
        return $this->hasMany(MessageReaction::class);
    }

    // Accessor for attachment URL
    public function getAttachmentUrlAttribute(): ?string
    {
        return $this->attachment_path ? Storage::url($this->attachment_path) : null;
    }
}
