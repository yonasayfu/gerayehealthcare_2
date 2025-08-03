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
            InventoryRequest::class
        );
    }

    public function create()
    {
        $staffList = Staff::all();
        $inventoryItems = InventoryItem::all();

        return Inertia::render('Admin/InventoryRequests/Create', [
            'staffList' => $staffList,
            'inventoryItems' => $inventoryItems,
        ]);
    }

    public function show(InventoryRequest $inventoryRequest)
    {
        return parent::show($inventoryRequest->id);
    }

    public function edit(InventoryRequest $inventoryRequest)
    {
        $staffList = Staff::all();
        $inventoryItems = InventoryItem::all();

        return Inertia::render('Admin/InventoryRequests/Edit', [
            'inventoryRequest' => $inventoryRequest->load(['requester', 'approver', 'item']),
            'staffList' => $staffList,
            'inventoryItems' => $inventoryItems,
        ]);
    }

    public function update(Request $request, InventoryRequest $inventoryRequest)
    {
        return parent::update($request, $inventoryRequest->id);
    }

    public function destroy(InventoryRequest $inventoryRequest)
    {
        return parent::destroy($inventoryRequest->id);
    }

    public function printSingle(InventoryRequest $inventoryRequest)
    {
        return parent::printSingle($inventoryRequest->id);
    }
}