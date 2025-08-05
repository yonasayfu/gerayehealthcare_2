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

    public function create(array|object $data): CorporateClient
    {
        $data = is_object($data) ? (array) $data : $data;
        return parent::create($data);
    }
}
