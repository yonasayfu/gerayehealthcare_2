<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\MarketingPlatform;
use App\Models\Staff;

use Illuminate\Support\Facades\DB;

class MarketingCampaign extends Model
{
    protected static function booted()
    {
        static::creating(function ($campaign) {
            if (empty($campaign->campaign_code)) {
                DB::transaction(function () use ($campaign) {
                    $latestCampaign = static::lockForUpdate()
                                            ->whereNotNull('campaign_code')
                                            ->orderBy('id', 'desc')
                                            ->first();

                    $nextNumber = 1;
                    if ($latestCampaign && preg_match('/CAM-(\d+)/', $latestCampaign->campaign_code, $matches)) {
                        $nextNumber = (int)$matches[1] + 1;
                    }

                    $campaign->campaign_code = 'CAM-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
                });
            }
        });
    }

    protected $fillable = [
        'campaign_code',
        'platform_id',
        'campaign_name',
        'campaign_type',
        'target_audience',
        'budget_allocated',
        'budget_spent',
        'start_date',
        'end_date',
        'status',
        'utm_campaign',
        'utm_source',
        'utm_medium',
        'assigned_staff_id',
        'created_by_staff_id',
        'goals',
    ];

    protected $casts = [
        'target_audience' => 'array',
        'goals' => 'array',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function platform()
    {
        return $this->belongsTo(MarketingPlatform::class);
    }

    public function assignedStaff()
    {
        return $this->belongsTo(Staff::class, 'assigned_staff_id');
    }

    public function createdByStaff()
    {
        return $this->belongsTo(Staff::class, 'created_by_staff_id');
    }
}
