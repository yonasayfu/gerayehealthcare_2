<?php

namespace App\Services\Insurance;

use App\Models\InsuranceCompany;
use App\DTOs\CreateInsuranceCompanyDTO;
use App\Services\BaseService;
use App\Http\Traits\ExportableTrait;
use App\Http\Config\AdditionalExportConfigs;
use Illuminate\Http\Request;

class InsuranceCompanyService extends BaseService
{
    use ExportableTrait;

    public function __construct(InsuranceCompany $insuranceCompany)
    {
        parent::__construct($insuranceCompany);
    }

    public function create(array|object $data): InsuranceCompany
    {
        $data = is_object($data) ? (array) $data : $data;
        return parent::create($data);
    }

    
}
