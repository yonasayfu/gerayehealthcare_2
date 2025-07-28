<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\MarketingCampaign;

use Illuminate\Support\Facades\DB;

class LandingPage extends Model
{
    protected static function booted()
    {
        static::creating(function ($landingPage) {
            if (empty($landingPage->page_code)) {
                DB::transaction(function () use ($landingPage) {
                    $latestLandingPage = static::lockForUpdate()
                                            ->whereNotNull('page_code')
                                            ->orderBy('id', 'desc')
                                            ->first();

                    $nextNumber = 1;
                    if ($latestLandingPage && preg_match('/LP-(\d+)/', $latestLandingPage->page_code, $matches)) {
                        $nextNumber = (int)$matches[1] + 1;
                    }

                    $landingPage->page_code = 'LP-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
                });
            }
        });
    }

    protected $fillable = [
        'page_code',
        'campaign_id',
        'page_title',
        'page_url',
        'template_used',
        'language',
        'form_fields',
        'conversion_goal',
        'views',
        'submissions',
        'conversion_rate',
        'is_active',
    ];

    protected $casts = [
        'form_fields' => 'array',
    ];

    public function campaign()
    {
        return $this->belongsTo(MarketingCampaign::class);
    }
}
