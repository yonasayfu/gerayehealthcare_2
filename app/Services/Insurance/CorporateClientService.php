<?php

namespace App\Services\Insurance;

use App\Models\CorporateClient;
use App\DTOs\CreateCorporateClientDTO;
use App\Services\BaseService;
use App\Http\Traits\ExportableTrait;
use App\Http\Config\AdditionalExportConfigs;
use Illuminate\Http\Request;

class CorporateClientService extends BaseService
{
    use ExportableTrait;

    public function __construct(CorporateClient $corporateClient)
    {
        parent::__construct($corporateClient);
    }

    public function create(array|object $data): CorporateClient
    {
        $data = is_object($data) ? (array) $data : $data;
        return parent::create($data);
    }

    public function export(Request $request)
    {
        return $this->handleExport($request, CorporateClient::class, AdditionalExportConfigs::getCorporateClientConfig());
    }
}
