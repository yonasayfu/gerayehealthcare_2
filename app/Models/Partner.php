<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Partner extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'contact_person',
        'email',
        'phone',
        'address',
        'engagement_status',
        'account_manager_id',
        'notes',
    ];

    public function agreements()
    {
        return $this->hasMany(PartnerAgreement::class);
    }

    public function referrals()
    {
        return $this->hasMany(Referral::class);
    }

    public function engagements()
    {
        return $this->hasMany(PartnerEngagement::class);
    }

    public function accountManager()
    {
        return $this->belongsTo(Staff::class, 'account_manager_id');
    }

    public function sharedInvoices()
    {
        return $this->hasMany(SharedInvoice::class);
    }
}
