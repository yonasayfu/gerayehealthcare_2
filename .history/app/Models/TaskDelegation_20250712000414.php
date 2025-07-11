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
    ];

    public function assignee()
    {
        return \$this->belongsTo(Staff::class, 'assigned_to');
    }
}