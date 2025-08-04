<?php

namespace App\Services\Insurance;

use App\Models\CorporateClient;
use App\DTOs\CreateCorporateClientDTO;
use App\Services\BaseService;

class CorporateClientService extends BaseService
{
    public function __construct(CorporateClient $corporateClient)
    {
        parent::__construct($corporateClient);
    }

    public function create(array $data): CorporateClient
    {
        return parent::create($data);
    }
}
