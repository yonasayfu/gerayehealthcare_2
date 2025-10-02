<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Lead extends Model
{
    use HasFactory;

    protected $table = 'marketing_leads';

    protected $fillable = [
        'lead_code',
        'source_campaign_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'country',
        'utm_source',
        'utm_campaign',
        'utm_medium',
        'landing_page_id',
        'lead_score',
        'status',
        'assigned_staff_id',
        'converted_patient_id',
        'conversion_date',
        'notes',
    ];

    protected $casts = [
        'conversion_date' => 'datetime',
        'lead_score' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the full name of the lead
     */
    public function getNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * Check if lead is converted
     */
    public function getConvertedToPatientAttribute(): bool
    {
        return !is_null($this->converted_patient_id);
    }

    /**
     * Get the campaign that this lead came from
     */
    public function campaign(): BelongsTo
    {
        return $this->belongsTo(MarketingCampaign::class, 'source_campaign_id');
    }

    /**
     * Get the staff member assigned to this lead
     */
    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'assigned_staff_id');
    }

    /**
     * Get the patient this lead was converted to
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class, 'converted_patient_id');
    }

    /**
     * Get the landing page this lead came from
     */
    public function landingPage(): BelongsTo
    {
        return $this->belongsTo(LandingPage::class);
    }

    /**
     * Scope to filter by status
     */
    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to filter converted leads
     */
    public function scopeConverted($query)
    {
        return $query->whereNotNull('converted_patient_id');
    }

    /**
     * Scope to filter unconverted leads
     */
    public function scopeUnconverted($query)
    {
        return $query->whereNull('converted_patient_id');
    }

    /**
     * Boot method to auto-generate lead code
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($lead) {
            if (empty($lead->lead_code)) {
                $lead->lead_code = self::generateLeadCode();
            }
        });
    }

    /**
     * Generate unique lead code
     */
    protected static function generateLeadCode(): string
    {
        $lastLead = self::orderBy('id', 'desc')->first();
        $number = $lastLead ? $lastLead->id + 1 : 1;
        return 'LEAD-' . str_pad($number, 5, '0', STR_PAD_LEFT);
    }
}
