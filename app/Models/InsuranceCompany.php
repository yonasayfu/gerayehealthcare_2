<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\InsurancePolicy;
use App\Models\InsuranceClaim;
use App\Models\Invoice;

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

    /**
     * Get the insurance policies for this company.
     */
    public function insurancePolicies()
    {
        return $this->hasMany(InsurancePolicy::class);
    }

    /**
     * Get the insurance claims for this company.
     */
    public function insuranceClaims()
    {
        return $this->hasMany(InsuranceClaim::class);
    }

    /**
     * Get the invoices associated with this insurance company.
     */
    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }
}
