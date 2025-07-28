<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\MarketingCampaign;
use App\Models\LandingPage;
use App\Models\Staff;
use App\Models\Patient;

use Illuminate\Support\Facades\DB;

class MarketingLead extends Model
{
    protected static function booted()
    {
        static::creating(function ($lead) {
            if (empty($lead->lead_code)) {
                DB::transaction(function () use ($lead) {
                    $latestLead = static::lockForUpdate()
                                            ->whereNotNull('lead_code')
                                            ->orderBy('id', 'desc')
                                            ->first();

                    $nextNumber = 1;
                    if ($latestLead && preg_match('/LEAD-(\d+)/', $latestLead->lead_code, $matches)) {
                        $nextNumber = (int)$matches[1] + 1;
                    }

                    $lead->lead_code = 'LEAD-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
                });
            }
        });
    }

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
    ];

    public function sourceCampaign()
    {
        return $this->belongsTo(MarketingCampaign::class, 'source_campaign_id');
    }

    public function landingPage()
    {
        return $this->belongsTo(LandingPage::class);
    }

    public function assignedStaff()
    {
        return $this->belongsTo(Staff::class, 'assigned_staff_id');
    }

    public function convertedPatient()
    {
        return $this->belongsTo(Patient::class, 'converted_patient_id');
    }
}
