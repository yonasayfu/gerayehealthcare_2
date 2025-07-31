<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InsurancePolicy extends Model
{
    use HasFactory;

    protected $fillable = [
        'insurance_company_id',
        'corporate_client_id',
        'service_type',
        'service_type_amharic',
        'coverage_percentage',
        'coverage_type',
        'is_active',
        'notes',
    ];
}
