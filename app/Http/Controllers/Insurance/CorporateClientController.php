<?php

namespace App\Http\Controllers\Insurance;

use App\DTOs\CreateCorporateClientDTO;
use App\Http\Controllers\Base\BaseController;
use App\Models\CorporateClient;
use App\Services\Insurance\CorporateClientService;
use App\Services\Validation\Rules\CorporateClientRules;

class CorporateClientController extends BaseController
{
    public function __construct(CorporateClientService $corporateClientService)
    {
        parent::__construct(
            $corporateClientService,
            CorporateClientRules::class,
            'Insurance/CorporateClients',
            'corporateClients',
            CorporateClient::class,
            CreateCorporateClientDTO::class
        );
    }
}
