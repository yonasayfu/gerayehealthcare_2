<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\MarketingCampaign;
use App\Models\MarketingPlatform;

class CampaignContent extends Model
{
    use HasFactory;
    protected $fillable = [
        'campaign_id',
        'platform_id',
        'content_type',
        'title',
        'description',
        'media_url',
        'scheduled_post_date',
        'actual_post_date',
        'status',
        'engagement_metrics',
    ];

    protected $casts = [
        'scheduled_post_date' => 'datetime',
        'actual_post_date' => 'datetime',
        'engagement_metrics' => 'array',
    ];

    public function campaign()
    {
        return $this->belongsTo(MarketingCampaign::class);
    }

    public function platform()
    {
        return $this->belongsTo(MarketingPlatform::class);
    }
}
