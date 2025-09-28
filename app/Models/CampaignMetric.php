<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CampaignMetric extends Model
{
    use HasFactory;

    protected $fillable = [
        'campaign_id',
        'date',
        'impressions',
        'clicks',
        'conversions',
        'cost_per_click',
        'cost_per_conversion',
        'roi_percentage',
        'leads_generated',
        'patients_acquired',
        'revenue_generated',
        'engagement_rate',
        'reach',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function campaign()
    {
        return $this->belongsTo(MarketingCampaign::class);
    }
}
