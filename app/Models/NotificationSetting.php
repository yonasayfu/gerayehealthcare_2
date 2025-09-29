<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'enable_push',
        'enable_email',
        'enable_sms',
        'channels',
    ];

    protected $casts = [
        'enable_push' => 'boolean',
        'enable_email' => 'boolean',
        'enable_sms' => 'boolean',
        'channels' => 'array',
    ];
}
