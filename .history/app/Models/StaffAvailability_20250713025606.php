<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon; // <-- Make sure to import Carbon

class StaffAvailability extends Model
{
    use HasFactory;

    protected $fillable = [
        'staff_id',
        'start_time',
        'end_time',
        'status',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    // --- START: THIS IS THE MISSING FIX ---
    // This function intercepts the start_time when it's read
    // and forces it into your local 'Africa/Addis_Ababa' timezone.
    public function getStartTimeAttribute($value): Carbon
    {
        return Carbon::parse($value)->setTimezone(config('app.timezone'));
    }

    // This does the same for the end_time.
    public function getEndTimeAttribute($value): Carbon
    {
        return Carbon::parse($value)->setTimezone(config('app.timezone'));
    }
    // --- END: THIS IS THE MISSING FIX ---

    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class);
    }
}