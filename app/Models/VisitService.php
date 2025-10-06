<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

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
        'service_type',
        'priority',
        'visit_location',
        'patient_location',
        'gps_coordinates',
        'patient_condition',
        'treatment_provided',
        'medications_administered',
        'follow_up_required',
        'follow_up_date',
        'follow_up_notes',
        'payment_status',
        'payment_method',
        'insurance_claim_id',
        'rating',
        'feedback',
        'cancellation_reason',
        'cancelled_at',
        'cancelled_by',
        'service_description',
        'prescription_file',
        'vitals_file',
        'status',
        'check_in_latitude',
        'check_in_longitude',
        'check_in_location',
        'check_out_latitude',
        'check_out_longitude',
        'check_out_location',
        'cost',
        'is_paid_to_staff',
    ];

    // Ensure safe defaults for DB NOT NULL columns
    protected $attributes = [
        'is_paid_to_staff' => false,
        'follow_up_required' => false,
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'check_in_time' => 'datetime',
        'check_out_time' => 'datetime',
        'follow_up_date' => 'date',
        'cancelled_at' => 'datetime',
        'is_paid_to_staff' => 'boolean',
        'cost' => 'decimal:2',
        'rating' => 'decimal:2',
        'follow_up_required' => 'boolean',
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

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(CaregiverAssignment::class, 'assignment_id');
    }

    // Add relationship to get the assigned staff through the assignment
    public function assignedStaff(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'staff_id');
    }

    public function staffPayouts(): BelongsToMany
    {
        return $this->belongsToMany(StaffPayout::class, 'payout_visit_service');
    }

    /**
     * Related invoice items created from this visit service.
     */
    public function invoiceItems(): HasMany
    {
        return $this->hasMany(InvoiceItem::class, 'visit_service_id');
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Get the URL for the prescription file.
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
     */
    public function getVitalsFileUrlAttribute(): ?string
    {
        if ($this->vitals_file) {
            return Storage::url($this->vitals_file);
        }

        return null;
    }
}
