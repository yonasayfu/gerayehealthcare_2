<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CorporateClient extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_name',
        'contact_person',
        'contact_email',
        'contact_phone',
        'tin_number',
        'trade_license_number',
        'address',
    ];
}
