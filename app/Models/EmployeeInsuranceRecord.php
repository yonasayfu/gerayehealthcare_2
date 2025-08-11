<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Patient;
use App\Models\InsurancePolicy;

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

    /**
     * Get the patient that owns this insurance record.
     */
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * Get the insurance policy for this record.
     */
    public function policy()
    {
        return $this->belongsTo(InsurancePolicy::class, 'policy_id');
    }
}
