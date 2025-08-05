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

    public function create(array|object $data): EligibilityCriteria
    {
        $data = is_object($data) ? (array) $data : $data;
        return parent::create($data);
    }

    // Add other methods as needed, e.g., update, delete, getById
}
