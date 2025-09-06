<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MarketingPlatform extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'api_endpoint',
        'api_credentials',
        'is_active',
    ];
}
