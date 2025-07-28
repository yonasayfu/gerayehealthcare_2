<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MarketingPlatform extends Model
{
    protected $fillable = [
        'name',
        'api_endpoint',
        'api_credentials',
        'is_active',
    ];
}
