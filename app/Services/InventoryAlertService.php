<?php

namespace App\Services;

use App\DTOs\CreateInventoryAlertDTO;
use App\Models\InventoryAlert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Traits\ExportableTrait;
use App\Http\Config\ExportConfig;

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

    public function getAll(Request $request, array $with = [])
    {
        $query = $this->model->with(array_merge(['item'], $with));

        if ($request->has('search')) {
            $this->applySearch($query, $request->input('search'));
        }

        // Optional filter: only active alerts
        if ($request->boolean('active_only', false)) {
            $query->where('is_active', true);
        }

        if ($request->has('sort')) {
            $direction = $request->input('direction', 'asc');
            $query->orderBy($request->input('sort'), $direction);
        } else {
            // Prioritize active alerts first, then recent
            $query->orderByDesc('is_active')
                  ->orderBy('created_at', 'desc');
        }

        return $query->paginate($request->input('per_page', 5))->withQueryString();
    }

    public function printAll(Request $request)
    {
        return $this->handlePrintAll($request, InventoryAlert::class, ExportConfig::getInventoryAlertConfig());
    }

    public function printCurrent(Request $request)
    {
        return $this->handlePrintCurrent($request, InventoryAlert::class, ExportConfig::getInventoryAlertConfig());
    }

    public function printSingle(InventoryAlert $inventoryAlert, Request $request)
    {
        return $this->handlePrintSingle($request, $inventoryAlert, ExportConfig::getInventoryAlertConfig());
    }

    public function count()
    {
        try {
            return InventoryAlert::where('is_active', true)->count();
        } catch (\Exception $e) {
            Log::error("Error fetching inventory alert count: " . $e->getMessage());
            return 0; // Return 0 or handle the error as appropriate
        }
    }
}
