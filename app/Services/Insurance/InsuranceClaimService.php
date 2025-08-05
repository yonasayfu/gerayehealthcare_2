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

    public function create(array|object $data): InsuranceClaim
    {
        $data = is_object($data) ? (array) $data : $data;
        return parent::create($data);
    }
}
