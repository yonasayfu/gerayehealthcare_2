<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskDelegation extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'assigned_to',
        'partner_id',
        'due_date',
        'status',
        'notes',
        'created_by',
        'acceptance_status',
        'response_notes',
        'responded_at',
        'responded_by',
        'task_date',
        'start_time',
        'end_time',
        'estimated_duration_minutes',
        'daily_notes',
        'task_category',
        'priority_level',
        'is_billable',
        'progress_status',
    ];

    protected $casts = [
        'due_date' => 'date',
        'task_date' => 'date',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'responded_at' => 'datetime',
        'priority_level' => 'integer',
        'is_billable' => 'boolean',
        'estimated_duration_minutes' => 'integer',
    ];

    // Add this to properly serialize dates for Inertia.js
    protected $appends = ['formatted_due_date'];

    // Accessor to format the due_date for frontend
    public function getFormattedDueDateAttribute()
    {
        return $this->due_date ? $this->due_date->format('Y-m-d') : null;
    }

    // Ensure proper serialization for Inertia.js
    public function toArray()
    {
        $array = parent::toArray();

        // Format dates properly for frontend consumption
        if ($this->due_date) {
            $array['due_date'] = $this->due_date->format('Y-m-d');
        }

        if ($this->task_date) {
            $array['task_date'] = $this->task_date->format('Y-m-d');
        }

        return $array;
    }

    public function assignee()
    {
        return $this->belongsTo(Staff::class, 'assigned_to');
    }

    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }

    public function creatorUser()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function responderUser()
    {
        return $this->belongsTo(User::class, 'responded_by');
    }
}
