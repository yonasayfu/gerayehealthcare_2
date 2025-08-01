<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventBroadcast extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'channel',
        'message',
        'sent_by_staff_id',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function sentByStaff()
    {
        return $this->belongsTo(Staff::class, 'sent_by_staff_id');
    }
}
