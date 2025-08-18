<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EligibilityCriteria extends Model
{
    use HasFactory;

    protected $table = 'eligibility_criteria';

    protected $fillable = [
        'event_id',
        'criteria_title',
        'operator',
        'value',
    ];

    // Relationships
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    // Accessors for export/print convenience
    public function getEventTitleAttribute(): ?string
    {
        return $this->event->title ?? null;
    }
}
