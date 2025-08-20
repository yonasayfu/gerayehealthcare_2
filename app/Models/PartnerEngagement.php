<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartnerEngagement extends Model
{
    use HasFactory;

    protected $fillable = [
        'partner_id',
        'staff_id',
        'engagement_type',
        'summary',
        'engagement_date',
        'follow_up_date',
    ];

    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }

    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }
}
