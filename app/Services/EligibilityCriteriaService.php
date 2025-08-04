<?php

namespace App\Services;

use App\Models\EligibilityCriteria;
use App\DTOs\CreateEligibilityCriteriaDTO;

class EligibilityCriteriaService extends BaseService
{
    public function __construct(EligibilityCriteria $eligibilityCriteria)
    {
        parent::__construct($eligibilityCriteria);
    }

    public function create(array $data): EligibilityCriteria
    {
        return parent::create($data);
    }

    // Add other methods as needed, e.g., update, delete, getById
}
