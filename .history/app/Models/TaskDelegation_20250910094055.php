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