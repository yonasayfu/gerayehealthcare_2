<?php

namespace App\Services\Insurance;

use App\Models\InsuranceClaim;
use App\DTOs\CreateInsuranceClaimDTO;
use App\Services\BaseService;

class InsuranceClaimService extends BaseService
{
    public function __construct(InsuranceClaim $insuranceClaim)
    {
        parent::__construct($insuranceClaim);
    }

    public function create(array $data): InsuranceClaim
    {
        return parent::create($data);
    }
}
