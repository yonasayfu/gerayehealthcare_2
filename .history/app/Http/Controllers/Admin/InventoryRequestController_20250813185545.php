<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Base\BaseController;
use App\Services\InventoryRequestService;
use App\Models\InventoryRequest;
use App\Models\Staff;
use App\Models\InventoryItem;
use App\Services\Validation\Rules\InventoryRequestRules;
use Illuminate\Http\Request;
use Inertia\Inertia;

class InventoryRequestController extends BaseController
{
    public function __construct(InventoryRequestService $inventoryRequestService)
    {
        parent::__construct(
            $inventoryRequestService,
            InventoryRequestRules::class,
            'Admin/InventoryRequests',
            'inventoryRequests',
            InventoryRequest::class,
            CreateInventoryRequestDTO::class
        );
    }

    public function create()
    {
        $staff = Staff::select('id', 'first_name', 'last_name')->orderBy('first_name')->get();
        $inventoryItems = InventoryItem::select('id', 'name')->orderBy('name')->get();

        return Inertia::render($this->viewName . '/Create', [
            'staff' => $staff,
            'inventoryItems' => $inventoryItems,
        ]);
    }

    public function edit($id)
    {
        $inventoryRequest = $this->service->getById($id);
        $staff = Staff::select('id', 'first_name', 'last_name')->orderBy('first_name')->get();
        $inventoryItems = InventoryItem::select('id', 'name')->orderBy('name')->get();

        return Inertia::render($this->viewName . '/Edit', [
            lcfirst(class_basename($this->modelClass)) => $inventoryRequest,
            'staff' => $staff,
            'inventoryItems' => $inventoryItems,
        ]);
    }
}
