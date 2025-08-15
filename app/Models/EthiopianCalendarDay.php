<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EthiopianCalendarDay extends Model
{
    use HasFactory;

    protected $fillable = [
        'gregorian_date',
        'ethiopian_date',
        'description',
        'is_holiday',
    ];
}
