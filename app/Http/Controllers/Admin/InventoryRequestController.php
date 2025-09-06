<?php

namespace App\Http\Controllers\Admin;

use App\DTOs\CreateInventoryRequestDTO;
use App\Http\Controllers\Base\BaseController;
use App\Http\Traits\ExportableTrait;
use App\Models\InventoryItem;
use App\Models\InventoryRequest;
use App\Models\Staff;
use App\Services\InventoryRequestService;
use App\Services\Validation\Rules\InventoryRequestRules;
use Illuminate\Http\Request;
use Inertia\Inertia;

class InventoryRequestController extends BaseController
{
    use ExportableTrait;

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

        return Inertia::render($this->viewName.'/Create', [
            'staffList' => $staff,
            'inventoryItems' => $inventoryItems,
        ]);
    }

    public function edit($id)
    {
        $inventoryRequest = $this->service->getById($id);
        $staff = Staff::select('id', 'first_name', 'last_name')->orderBy('first_name')->get();
        $inventoryItems = InventoryItem::select('id', 'name')->orderBy('name')->get();

        return Inertia::render($this->viewName.'/Edit', [
            lcfirst(class_basename($this->modelClass)) => $inventoryRequest,
            'staffList' => $staff,
            'inventoryItems' => $inventoryItems,
        ]);
    }

    /**
     * Print a single inventory request as PDF.
     */
    public function printSingle(Request $request, $id)
    {
        $model = InventoryRequest::with(['requester', 'approver', 'item'])->findOrFail($id);
        $config = $this->buildExportConfig();

        return $this->handlePrintSingle($request, $model, $config);
    }

    /**
     * Build export/print configuration for Inventory Requests compatible with ExportableTrait.
     */
    private function buildExportConfig(): array
    {
        // Define columns for PDF layouts using dot-notation keys
        $pdfColumns = [
            ['key' => 'requester.first_name', 'label' => 'Requester First Name'],
            ['key' => 'requester.last_name', 'label' => 'Requester Last Name'],
            ['key' => 'item.name', 'label' => 'Item'],
            ['key' => 'quantity_requested', 'label' => 'Quantity'],
            ['key' => 'status', 'label' => 'Status'],
            ['key' => 'priority', 'label' => 'Priority'],
            ['key' => 'needed_by_date', 'label' => 'Needed By'],
        ];

        return [
            // Eager-load relations for all variants
            'with_relations' => ['requester', 'approver', 'item'],

            'pdf' => [
                'view' => 'pdf-layout',
                'document_title' => 'Inventory Requests',
                'filename_prefix' => 'inventory_requests',
                'orientation' => 'landscape',
                'with_relations' => ['requester', 'approver', 'item'],
                'columns' => $pdfColumns,
            ],
            'all_records' => [
                'view' => 'pdf-layout',
                'document_title' => 'Inventory Requests List',
                'filename_prefix' => 'inventory_requests',
                'orientation' => 'landscape',
                'include_index' => true,
                'with_relations' => ['requester', 'approver', 'item'],
                'columns' => $pdfColumns,
            ],
            'current_page' => [
                'view' => 'pdf-layout',
                'document_title' => 'Inventory Requests (Current View)',
                'filename_prefix' => 'inventory_requests_current',
                'orientation' => 'landscape',
                'with_relations' => ['requester', 'approver', 'item'],
                'columns' => $pdfColumns,
            ],
            'single_record' => [
                'view' => 'pdf-layout',
                'document_title' => 'Inventory Request Details',
                'filename_prefix' => 'inventory_request',
                'with_relations' => ['requester', 'approver', 'item'],
                // columns optional for single page; Blade can render fields
            ],
        ];
    }

    // Ensure ExportableTrait can apply filters/search/sorting
    protected function applySearch($query, $search)
    {
        if (! $search) {
            return $query;
        }

        return $query->where(function ($q) use ($search) {
            $q->whereHas('item', function ($q) use ($search) {
                $q->where('name', 'ilike', "%{$search}%");
            })
                ->orWhereHas('requester', function ($q) use ($search) {
                    $q->where('first_name', 'ilike', "%{$search}%")
                        ->orWhere('last_name', 'ilike', "%{$search}%");
                })
                ->orWhere('reason', 'ilike', "%{$search}%");
        });
    }

    protected function applySorting($query, Request $request)
    {
        if ($request->filled('sort')) {
            $sortField = $request->input('sort');
            $sortDirection = $request->input('direction', 'asc');

            if ($sortField === 'requester_id') {
                $query->join('staff', 'inventory_requests.requester_id', '=', 'staff.id')
                    ->orderBy('staff.first_name', $sortDirection)
                    ->select('inventory_requests.*');
            } elseif ($sortField === 'item_id') {
                $query->join('inventory_items', 'inventory_requests.item_id', '=', 'inventory_items.id')
                    ->orderBy('inventory_items.name', $sortDirection)
                    ->select('inventory_requests.*');
            } else {
                $query->orderBy($sortField, $sortDirection);
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }

        return $query;
    }
}
