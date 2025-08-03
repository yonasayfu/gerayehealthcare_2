<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Base\BaseController;
use App\Services\InventoryAlertService;
use App\Models\InventoryAlert;
use App\Services\Validation\Rules\InventoryAlertRules;
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
            InventoryAlert::class
        );
    }

    public function show(InventoryAlert $inventoryAlert)
    {
        return parent::show($inventoryAlert->id);
    }

    public function edit(InventoryAlert $inventoryAlert)
    {
        return parent::edit($inventoryAlert->id);
    }

    public function update(Request $request, InventoryAlert $inventoryAlert)
    {
        return parent::update($request, $inventoryAlert->id);
    }

    public function destroy(InventoryAlert $inventoryAlert)
    {
        return parent::destroy($inventoryAlert->id);
    }

    public function printSingle(InventoryAlert $inventoryAlert)
    {
        return parent::printSingle($inventoryAlert->id);
    }

    public function count()
    {
        return $this->service->count();
    }
}