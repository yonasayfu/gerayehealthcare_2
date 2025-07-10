<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Service;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Storage;

class VisitService extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'staff_id',
        'service_id', // <-- This is the new field
        'scheduled_at',
        'check_in_time',
        'check_out_time',
        'visit_notes',
        // 'service_description' has been removed
        'prescription_file',
        'vitals_file',
        'status',
        'cost',
        'is_paid_to_staff',
        'is_invoiced',
        'check_in_latitude',
        'check_in_longitude',
        'check_out_latitude',
        'check_out_longitude',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'check_in_time' => 'datetime',
        'check_out_time' => 'datetime',
        'is_invoiced' => 'boolean',
        'is_paid_to_staff' => 'boolean',
    ];

    protected $appends = [
        'prescription_file_url',
        'vitals_file_url',
    ];

    // --- NEW RELATIONSHIP ---
    /**
     * The service that was provided during this visit.
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class);
    }

    public function staffPayouts(): BelongsToMany
    {
        return $this->belongsToMany(StaffPayout::class, 'payout_visit_service');
    }

    public function invoiceItem(): HasOne
    {
        return $this->hasOne(InvoiceItem::class);
    }

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
