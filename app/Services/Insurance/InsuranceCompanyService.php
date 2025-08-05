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

    public function create(array|object $data): InsuranceCompany
    {
        $data = is_object($data) ? (array) $data : $data;
        return parent::create($data);
    }
}
