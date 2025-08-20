<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventStaffAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'staff_id',
        'role',
        'notes',
    ];

    /**
     * The event associated with this assignment.
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * The staff member associated with this assignment.
     */
    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class);
    }
}
