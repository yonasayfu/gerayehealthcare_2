<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventRecommendation extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'source_channel',
        'recommended_by_name',
        'recommended_by_phone',
        'patient_name',
        'phone_number',
        'notes',
        'status',
    ];
}
