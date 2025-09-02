<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Reaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'reactable_type',
        'reactable_id',
        'user_id',
        'emoji',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the parent reactable model (Message, GroupMessage, etc.)
     */
    public function reactable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the user who made the reaction
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
