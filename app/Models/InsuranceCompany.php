<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InsuranceCompany extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'name_amharic',
        'contact_person',
        'contact_email',
        'contact_phone',
        'address',
        'address_amharic',
    ];
}
