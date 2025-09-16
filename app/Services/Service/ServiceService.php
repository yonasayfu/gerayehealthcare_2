<?php

namespace App\Services\Service;

use App\Http\Config\ExportConfig;
use App\Http\Traits\ExportableTrait;
use App\Models\Service;
use App\Services\Base\BaseService;
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

    public function getAll(Request $request, array $with = [])
    {
        $query = $this->model->newQuery()->with($with);

        // Search
        if ($request->filled('search')) {
            $this->applySearch($query, $request->input('search'));
        }

        // Status filter: All | Active | Inactive
        $status = $request->input('status');
        if ($status === 'Active') {
            $query->where('is_active', true);
        } elseif ($status === 'Inactive') {
            $query->where('is_active', false);
        }

        // Sorting
        $allowedSorts = ['name', 'price', 'is_active', 'created_at'];
        $sort = $request->input('sort');
        $direction = strtolower((string) $request->input('direction', 'asc')) === 'desc' ? 'desc' : 'asc';
        if ($sort && in_array($sort, $allowedSorts, true)) {
            $query->orderBy($sort, $direction);
        } else {
            $query->orderBy('name', 'asc');
        }

        return $query->paginate((int) $request->input('per_page', 10));
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
