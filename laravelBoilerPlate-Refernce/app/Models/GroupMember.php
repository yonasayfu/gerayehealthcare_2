<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GroupMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'group_id',
        'user_id',
        'role',
        'joined_at',
    ];

    protected $casts = [
        'joined_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Member roles
     */
    const ROLE_OWNER = 'owner';
    const ROLE_ADMIN = 'admin';
    const ROLE_MEMBER = 'member';

    /**
     * Get the group this member belongs to
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    /**
     * Get the user for this group member
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if this member is an admin
     */
    public function isAdmin(): bool
    {
        return in_array($this->role, [self::ROLE_ADMIN, self::ROLE_OWNER]);
    }

    /**
     * Check if this member is the owner
     */
    public function isOwner(): bool
    {
        return $this->role === self::ROLE_OWNER;
    }

    /**
     * Scope for admins
     */
    public function scopeAdmins($query)
    {
        return $query->whereIn('role', [self::ROLE_ADMIN, self::ROLE_OWNER]);
    }

    /**
     * Scope for owners
     */
    public function scopeOwners($query)
    {
        return $query->where('role', self::ROLE_OWNER);
    }
}
