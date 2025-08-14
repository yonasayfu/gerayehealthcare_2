<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Base\BaseController;
use App\Services\InventoryAlertService;
use App\Models\InventoryAlert;
use App\Services\Validation\Rules\InventoryAlertRules;
use Illuminate\Http\Request;
use App\Http\Controllers\Base\BaseController;

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

    public function count()
    {
        return response()->json(['count' => $this->service->count()]);
    }
}