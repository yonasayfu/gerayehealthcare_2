<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventRecommendation extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'source',
        'recommended_by',
        'patient_name',
        'patient_phone',
        'notes',
        'status',
    ];
}
