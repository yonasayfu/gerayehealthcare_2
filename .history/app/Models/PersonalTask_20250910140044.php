<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PersonalTask extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'title', 'notes', 'due_date', 'is_completed', 'is_important', 'reminder_at', 'reminded_at',
        'my_day_for', 'recurrence_type', 'recurrence_weekdays', 'task_date', 'start_time', 'end_time',
        'estimated_duration_minutes', 'daily_notes', 'task_category', 'priority_level', 'is_billable'
    ];

    protected $casts = [
        'is_completed' => 'boolean',
        'is_important' => 'boolean',
        'due_date' => 'datetime',
        'reminder_at' => 'datetime',
        'reminded_at' => 'datetime',
        'my_day_for' => 'date',
        'task_date' => 'date',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'recurrence_weekdays' => 'array',
        'priority_level' => 'integer',
        'is_billable' => 'boolean',
        'estimated_duration_minutes' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function subtasks()
    {
        return $this->hasMany(PersonalTaskSubtask::class)->orderBy('position');
    }
}