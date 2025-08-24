<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceVolumeView extends Model
{
    protected $table = 'service_volume_view';
    public $timestamps = false;
    protected $primaryKey = null;
    public $incrementing = false;

    protected $guarded = [];

    protected $casts = [
        'bucket_date' => 'date',
        'bucket_label' => 'string',
        'granularity' => 'string',
        'service_category' => 'string',
        'total_visits' => 'integer',
        'unique_patients' => 'integer',
        'is_event_service' => 'boolean',
    ];
}
