<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
// Import Storage facade

class VisitService extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'staff_id',
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

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'prescription_file_url',
        'vitals_file_url',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class);
    }

    /**
     * Get the URL for the prescription file.
     *
     * @return string|null
     */
    public function getPrescriptionFileUrlAttribute(): ?string
    {
        if ($this->prescription_file) {
            return Storage::url($this->prescription_file);
        }
        return null;
    }

    /**
     * Get the URL for the vitals file.
     *
     * @return string|null
     */
    public function getVitalsFileUrlAttribute(): ?string
    {
        if ($this->vitals_file) {
            return Storage::url($this->vitals_file);
        }
        return null;
    }
}
