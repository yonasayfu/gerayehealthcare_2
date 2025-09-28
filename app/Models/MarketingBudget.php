<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MarketingBudget extends Model
{
    use HasFactory;

    protected $fillable = [
        'campaign_id',
        'platform_id',
        'budget_name',
        'description',
        'allocated_amount',
        'spent_amount',
        'period_start',
        'period_end',
        'status',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
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
