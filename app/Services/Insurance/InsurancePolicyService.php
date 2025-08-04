<?php

namespace App\Services\Insurance;

use App\Models\InsurancePolicy;
use App\DTOs\CreateInsurancePolicyDTO;
use App\Services\BaseService;

class InsurancePolicyService extends BaseService
{
    public function __construct(InsurancePolicy $insurancePolicy)
    {
        parent::__construct($insurancePolicy);
    }

    public function create(array $data): InsurancePolicy
    {
        return parent::create($data);
    }
}
