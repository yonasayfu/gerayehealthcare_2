<?php

namespace App\Services\Insurance;

use App\Models\InsuranceCompany;
use App\DTOs\CreateInsuranceCompanyDTO;
use App\Services\BaseService;

class InsuranceCompanyService extends BaseService
{
    public function __construct(InsuranceCompany $insuranceCompany)
    {
        parent::__construct($insuranceCompany);
    }

    public function create(array $data): InsuranceCompany
    {
        return parent::create($data);
    }
}
