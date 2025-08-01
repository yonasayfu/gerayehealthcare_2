<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventStaffAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'staff_id',
        'role',
    ];
}
