<?php

namespace App\Services\Insurance;

use App\Models\InsuranceCompany;
use App\DTOs\CreateInsuranceCompanyDTO;
use App\Services\BaseService;
use Illuminate\Http\Request;

class InsuranceCompanyService extends BaseService
{
    public function __construct(InsuranceCompany $insuranceCompany)
    {
        parent::__construct($insuranceCompany);
    }

    public function create(array|object $data): InsuranceCompany
    {
        $data = is_object($data) ? (array) $data : $data;
        return parent::create($data);
    }

    protected function applySearch($query, $search)
    {
        $query->where('name', 'ilike', "%{$search}%")
              ->orWhere('contact_person', 'ilike', "%{$search}%")
              ->orWhere('contact_email', 'ilike', "%{$search}%")
              ->orWhere('contact_phone', 'ilike', "%{$search}%")
              ->orWhere('address', 'ilike', "%{$search}%");
    }

    
}
