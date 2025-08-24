<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MarketingRoiView extends Model
{
    protected $table = 'marketing_roi_view';
    public $timestamps = false;
    protected $primaryKey = null;
    public $incrementing = false;

    protected $guarded = [];

    protected $casts = [
        'bucket_date' => 'date',
        'bucket_label' => 'string',
        'granularity' => 'string',
        'platform' => 'string',
        'impressions' => 'integer',
        'clicks' => 'integer',
        'conversions' => 'integer',
        'revenue_generated' => 'decimal:2',
        'spend' => 'decimal:2',
        'roi_percentage' => 'decimal:2',
    ];
}
