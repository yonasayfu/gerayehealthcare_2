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
        'progress_status'
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

    public function assignee()
    {
        return $this->belongsTo(Staff::class, 'assigned_to');
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