<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartnerAgreement extends Model
{
    use HasFactory;

    protected $fillable = [
        'partner_id',
        'agreement_title',
        'agreement_type',
        'status',
        'start_date',
        'end_date',
        'priority_service_level',
        'commission_type',
        'commission_rate',
        'terms_document_path',
        'signed_by_staff_id',
    ];

    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }

    public function referrals()
    {
        return $this->hasMany(Referral::class);
    }

    public function commissions()
    {
        return $this->hasMany(PartnerCommission::class);
    }

    public function signedBy()
    {
        return $this->belongsTo(Staff::class, 'signed_by_staff_id');
    }
}
