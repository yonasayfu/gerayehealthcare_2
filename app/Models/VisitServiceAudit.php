<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisitServiceAudit extends Model
{
    use HasFactory;

    protected $fillable = [
        'visit_service_id',
        'changed_by_user_id',
        'from_check_in_time',
        'to_check_in_time',
        'from_check_out_time',
        'to_check_out_time',
        'reason',
    ];
}

