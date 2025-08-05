<?php

namespace App\Services;

use App\DTOs\CreateServiceDTO;
use App\Models\Service;
use App\Http\Traits\ExportableTrait;
use App\Http\Config\ExportConfig;
use Illuminate\Http\Request;

class ServiceService extends BaseService
{
    use ExportableTrait;

    public function __construct(Service $service)
    {
        parent::__construct($service);
    }

    protected function applySearch($query, $search)
    {
        $query->where('name', 'ilike', "%{$search}%");
    }

    public function export(Request $request)
    {
        return $this->handleExport($request, Service::class, ExportConfig::getServiceConfig());
    }

    public function printSingle($id, Request $request)
    {
        $service = $this->getById($id);
        $config = ExportConfig::getServiceConfig();
        
        return $this->handlePrintSingle($request, $service, $config);
    }

    public function printCurrent(Request $request)
    {
        return $this->handlePrintCurrent($request, Service::class, ExportConfig::getServiceConfig());
    }

    public function printAll(Request $request)
    {
        return $this->handlePrintAll($request, Service::class, ExportConfig::getServiceConfig());
    }
}
