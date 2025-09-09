<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PersonalTaskSubtask extends Model
{
    use HasFactory;

    protected $fillable = [
        'personal_task_id', 'title', 'is_completed', 'position'
    ];

    protected $casts = [
        'is_completed' => 'boolean',
    ];

    public function task()
    {
        return $this->belongsTo(PersonalTask::class, 'personal_task_id');
    }
}

