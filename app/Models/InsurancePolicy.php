<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\InsuranceCompany;
use App\Models\CorporateClient;
use App\Models\EmployeeInsuranceRecord;
use App\Models\InsuranceClaim;

class InsurancePolicy extends Model
{
    use HasFactory;

    protected $fillable = [
        'insurance_company_id',
        'corporate_client_id',
        'service_type',
        'coverage_percentage',
        'coverage_type',
        'is_active',
        'notes',
    ];

    /**
     * Get the insurance company that owns this policy.
     */
    public function insuranceCompany()
    {
        return $this->belongsTo(InsuranceCompany::class);
    }

    /**
     * Get the corporate client that owns this policy.
     */
    public function corporateClient()
    {
        return $this->belongsTo(CorporateClient::class);
    }

    /**
     * Get the employee insurance records using this policy.
     */
    public function employeeInsuranceRecords()
    {
        return $this->hasMany(EmployeeInsuranceRecord::class, 'policy_id');
    }

    /**
     * Get the insurance claims using this policy.
     */
    public function insuranceClaims()
    {
        return $this->hasMany(InsuranceClaim::class, 'policy_id');
    }
}
