<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\MarketingTask;
use Illuminate\Support\Facades\Storage;

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
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'hourly_rate' => 'float',
    ];

    /**
     * Get the staff's full name.
     *
     * @return string
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['full_name', 'photo_url'];

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

    public function doctorTasks(): HasMany
    {
        return $this->hasMany(MarketingTask::class, 'doctor_id');
    }

    /**
     * Get the public URL for the staff photo on the configured public disk.
     */
    public function getPhotoUrlAttribute(): ?string
    {
        if (!$this->photo) {
            return null;
        }
        // Use the 'public' disk so this works regardless of APP_URL changes
        return Storage::disk('public')->url($this->photo);
    }

    /**
     * Documents uploaded by this staff member.
     */
    public function referralDocuments(): HasMany
    {
        return $this->hasMany(ReferralDocument::class, 'uploaded_by_staff_id');
    }
}
