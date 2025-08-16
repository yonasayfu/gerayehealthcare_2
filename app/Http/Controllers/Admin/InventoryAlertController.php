<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Base\BaseController;
use App\Services\InventoryAlertService;
use App\Models\InventoryAlert;
use App\Services\Validation\Rules\InventoryAlertRules;
use App\DTOs\CreateInventoryAlertDTO;
use Illuminate\Http\Request;

class InventoryAlertController extends BaseController
{
    public function __construct(InventoryAlertService $inventoryAlertService)
    {
        parent::__construct(
            $inventoryAlertService,
            InventoryAlertRules::class,
            'Admin/InventoryAlerts',
            'inventoryAlerts',
            InventoryAlert::class,
            CreateInventoryAlertDTO::class
        );
    }

    public function show($id)
    {
        $alert = $this->service->getById($id, ['item', 'delegatedTask.assignee']);
        return \Inertia\Inertia::render('Admin/InventoryAlerts/Show', [
            'inventoryAlert' => $alert,
            'returnTo' => request()->input('return_to'),
        ]);
    }

    public function count()
    {
        return response()->json(['count' => $this->service->count()]);
    }

    public function printAll()
    {
        return app(InventoryAlertService::class)->printAll(request());
    }

    public function printCurrent()
    {
        return app(InventoryAlertService::class)->printCurrent(request());
    }

    public function printSingle(InventoryAlert $inventory_alert)
    {
        return app(InventoryAlertService::class)->printSingle($inventory_alert, request());
    }
}
