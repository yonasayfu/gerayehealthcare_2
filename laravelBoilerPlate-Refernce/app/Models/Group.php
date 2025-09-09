<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Group extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'created_by',
        'is_private',
        'max_members',
    ];

    protected $casts = [
        'is_private' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the user who created the group
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get all group members
     */
    public function members(): HasMany
    {
        return $this->hasMany(GroupMember::class);
    }

    /**
     * Get all group messages
     */
    public function messages(): HasMany
    {
        return $this->hasMany(GroupMessage::class);
    }

    /**
     * Get users in this group with their roles
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'group_members')
                    ->withPivot('role', 'joined_at')
                    ->withTimestamps();
    }

    /**
     * Get group admins
     */
    public function admins(): BelongsToMany
    {
        return $this->users()->wherePivot('role', 'admin');
    }

    /**
     * Get group owners
     */
    public function owners(): BelongsToMany
    {
        return $this->users()->wherePivot('role', 'owner');
    }

    /**
     * Check if user is a member of this group
     */
    public function hasMember(int $userId): bool
    {
        return $this->members()->where('user_id', $userId)->exists();
    }

    /**
     * Check if user is an admin of this group
     */
    public function isAdmin(int $userId): bool
    {
        return $this->members()
                    ->where('user_id', $userId)
                    ->whereIn('role', ['admin', 'owner'])
                    ->exists();
    }

    /**
     * Check if user is the owner of this group
     */
    public function isOwner(int $userId): bool
    {
        return $this->members()
                    ->where('user_id', $userId)
                    ->where('role', 'owner')
                    ->exists();
    }

    /**
     * Get member count
     */
    public function getMemberCountAttribute(): int
    {
        return $this->members()->count();
    }

    /**
     * Get latest message
     */
    public function getLatestMessageAttribute(): ?GroupMessage
    {
        return $this->messages()->latest()->first();
    }
}
