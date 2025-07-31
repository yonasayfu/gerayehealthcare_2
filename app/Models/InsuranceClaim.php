<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
    ];
}
