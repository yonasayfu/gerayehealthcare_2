<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Carbon; // <-- Import Carbon

class VisitService extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'staff_id',
        'assignment_id',
        'scheduled_at',
        'check_in_time',
        'check_out_time',
        'visit_notes',
        'prescription_file',
        'vitals_file',
        'status',
        'check_in_latitude',
        'check_in_longitude',
        'check_out_latitude',
        'check_out_longitude',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'check_in_time' => 'datetime',
        'check_out_time' => 'datetime',
    ];
    
    protected $appends = [
        'prescription_file_url',
        'vitals_file_url',
    ];

    // --- START: NEW TIMEZONE FIX ---
    public function getScheduledAtAttribute($value): Carbon
    {
        return Carbon::parse($value)->setTimezone(config('app.timezone'));
    }
    // --- END: NEW TIMEZONE FIX ---
    
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class);
    }
    
    // ... your other get...UrlAttribute methods
    public function getPrescriptionFileUrlAttribute(): ?string
    {
        if ($this->prescription_file) {
            return Storage::url($this->prescription_file);
        }
        return null;
    }

    public function getVitalsFileUrlAttribute(): ?string
    {
        if ($this->vitals_file) {
            return Storage::url($this->vitals_file);
        }
        return null;
    }
}