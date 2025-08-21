<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartnerCommission extends Model
{
    use HasFactory;

    protected $fillable = [
        'agreement_id',
        'referral_id',
        'invoice_id',
        'commission_amount',
        'calculation_date',
        'payout_date',
        'status',
    ];

    public function agreement()
    {
        return $this->belongsTo(PartnerAgreement::class);
    }

    public function referral()
    {
        return $this->belongsTo(Referral::class);
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}
