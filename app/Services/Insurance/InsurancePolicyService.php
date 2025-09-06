<?php

namespace App\Services\Insurance;

use App\Models\InsurancePolicy;
use App\Services\BaseService;

class InsurancePolicyService extends BaseService
{
    public function __construct(InsurancePolicy $insurancePolicy)
    {
        parent::__construct($insurancePolicy);
    }

    public function create(array|object $data): InsurancePolicy
    {
        $data = is_object($data) ? (array) $data : $data;

        return parent::create($data);
    }
}
