<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Referral extends Model
{
    use HasFactory;

    protected $fillable = [
        'partner_id',
        'agreement_id',
        'referred_patient_id',
        'referral_date',
        'status',
        'notes',
    ];

    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }

    public function agreement()
    {
        return $this->belongsTo(PartnerAgreement::class, 'agreement_id');
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class, 'referred_patient_id');
    }

    public function commission()
    {
        return $this->hasOne(PartnerCommission::class);
    }

    public function referralDocuments()
    {
        return $this->hasMany(ReferralDocument::class);
    }
}
