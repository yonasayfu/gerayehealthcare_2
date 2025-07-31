<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeInsuranceRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'policy_id',
        'kebele_id',
        'woreda',
        'region',
        'federal_id',
        'ministry_department',
        'employee_id_number',
        'verified',
        'verified_at',
    ];
}
