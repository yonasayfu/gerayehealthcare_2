<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Quote extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'text',
        'language',
        'author',
        'pinned',
        'priority',
        'image_path',
    ];

    protected $casts = [
        'pinned' => 'boolean',
        'priority' => 'integer',
    ];

    protected $appends = [
        'image_url',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getImageUrlAttribute(): ?string
    {
        if (! $this->image_path) {
            return null;
        }
        // Use storage URL helper; ensure storage:link is created
        return \Storage::disk('public')->url($this->image_path);
    }
}
