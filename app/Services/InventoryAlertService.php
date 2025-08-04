<?php

namespace App\Services;

use App\DTOs\CreateInventoryAlertDTO;
use App\Models\InventoryAlert;
use Illuminate\Http\Request;
use App\Http\Traits\ExportableTrait;
use App\Http\Config\AdditionalExportConfigs;

class InventoryAlertService extends BaseService
{
    use ExportableTrait;

    public function __construct(InventoryAlert $inventoryAlert)
    {
        parent::__construct($inventoryAlert);
    }

    protected function applySearch($query, $search)
    {
        return $query->where('alert_type', 'like', "%$search%")
                  ->orWhere('message', 'like', "%$search%")
                  ->orWhereHas('item', fn($q) => $q->where('name', 'like', "%$search%"));
    }

    public function getAll(Request $request)
    {
        $query = $this->model->with(['item']);

        if ($request->has('search')) {
            $this->applySearch($query, $request->input('search'));
        }

        if ($request->has('sort')) {
            $direction = $request->input('direction', 'asc');
            $query->orderBy($request->input('sort'), $direction);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        return $query->paginate($request->input('per_page', 10));
    }

    public function export(Request $request)
    {
        return $this->handleExport($request, InventoryAlert::class, AdditionalExportConfigs::getInventoryAlertConfig());
    }

    public function printAll(Request $request)
    {
        return $this->handlePrintAll($request, InventoryAlert::class, AdditionalExportConfigs::getInventoryAlertConfig());
    }

    public function printCurrent(Request $request)
    {
        return $this->handlePrintCurrent($request, InventoryAlert::class, AdditionalExportConfigs::getInventoryAlertConfig());
    }

    public function printSingle($id)
    {
        $inventoryAlert = $this->getById($id);
        return $this->handlePrintSingle($inventoryAlert, AdditionalExportConfigs::getInventoryAlertConfig());
    }

    public function count()
    {
        return InventoryAlert::where('is_active', true)->count();
    }
}
