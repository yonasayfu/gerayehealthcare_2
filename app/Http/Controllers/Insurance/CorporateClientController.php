<?php

namespace App\Http\Controllers\Insurance;

use App\Http\Controllers\Base\BaseController;
use App\Http\Traits\ExportableTrait;
use App\Http\Config\AdditionalExportConfigs;
use App\Models\CorporateClient;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Inertia\Inertia;
use App\Http\Requests\StoreCorporateClientRequest;
use App\Http\Requests\UpdateCorporateClientRequest;

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
