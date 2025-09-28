<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InsuranceClaim extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'invoice_id',
        'insurance_company_id',
        'policy_id',
        'claim_status',
        'coverage_amount',
        'paid_amount',
        'submitted_at',
        'processed_at',
        'payment_due_date',
        'payment_received_at',
        'payment_method',
        'reimbursement_required',
        'receipt_number',
        'is_pre_authorized',
        'pre_authorization_code',
        'denial_reason',
        'translated_notes',
        'email_sent_at',
        'email_status',
    ];

    protected $casts = [
        'email_sent_at' => 'datetime',
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * Get the insurance company for this claim.
     */
    public function insuranceCompany(): BelongsTo
    {
        return $this->belongsTo(InsuranceCompany::class);
    }

    /**
     * Get the insurance policy for this claim.
     */
    public function policy(): BelongsTo
    {
        return $this->belongsTo(InsurancePolicy::class, 'policy_id');
    }
}
