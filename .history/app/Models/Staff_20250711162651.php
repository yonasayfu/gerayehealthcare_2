<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Staff extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'position',
        'department',
        'role',
        'status',
        'hire_date',
        'photo',
        'user_id',
        'hourly_rate',
    ];

    /**
     * The user account associated with the staff member.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all of the availability slots for the staff member.
     * THIS IS THE FIX.
     */
    public function availabilities(): HasMany
    {
        return $this->hasMany(StaffAvailability::class);
    }

    /**
     * Get all of the visit services for the staff member.
     */
    public function visitServices(): HasMany
    {
        return $this->hasMany(VisitService::class);
    }

    /**
     * Get the payout history for the staff member.
     */
    public function payouts(): HasMany
    {
        return $this->hasMany(StaffPayout::class);
    }
    /**
     * Get the leave requests for the staff member.
     */
    public function leaveRequests(): HasMany // <-- ADD THIS FUNCTION
    {
        return $this->hasMany(LeaveRequest::class);
    }
}
