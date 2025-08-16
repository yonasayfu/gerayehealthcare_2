<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Base\BaseController;
use App\Services\InventoryItemService;
use App\Models\InventoryItem;
use App\Services\Validation\Rules\InventoryItemRules;
use App\Models\Supplier;
use App\Http\Traits\ExportableTrait;
use App\Http\Config\AdditionalExportConfigs;
use Illuminate\Http\Request;
use Inertia\Inertia;

class InventoryItemController extends BaseController
{
    use ExportableTrait;

    public function __construct(InventoryItemService $inventoryItemService)
    {
        parent::__construct(
            $inventoryItemService,
            InventoryItemRules::class,
            'Admin/InventoryItems',
            'inventoryItems',
            InventoryItem::class
        );
    }

    public function create()
    {
        return Inertia::render('Admin/InventoryItems/Create', [
            'suppliers' => Supplier::query()->select('id', 'name')->orderBy('name')->get(),
        ]);
    }

    public function edit($id)
    {
        $data = $this->service->getById($id);
        $propName = lcfirst(class_basename($this->modelClass));
        return Inertia::render('Admin/InventoryItems/Edit', [
            $propName => $data,
            'suppliers' => Supplier::query()->select('id', 'name')->orderBy('name')->get(),
        ]);
    }

    /**
     * Export all records as CSV or PDF based on request type param.
     */
    public function export(Request $request)
    {
        $config = $this->buildExportConfig();
        return $this->handleExport($request, InventoryItem::class, $config);
    }

    /**
     * Print all records (PDF).
     */
    public function printAll(Request $request)
    {
        $config = $this->buildExportConfig();
        return $this->handlePrintAll($request, InventoryItem::class, $config);
    }

    /**
     * Print current page/view (PDF with pagination and current filters).
     */
    public function printCurrent(Request $request)
    {
        $config = $this->buildExportConfig();
        return $this->handlePrintCurrent($request, InventoryItem::class, $config);
    }

    /**
     * Print a single record by id.
     */
    public function printSingle(Request $request, $id)
    {
        $model = InventoryItem::findOrFail($id);
        $config = $this->buildExportConfig();
        return $this->handlePrintSingle($request, $model, $config);
    }

    /**
     * Normalize AdditionalExportConfigs for Inventory Items to ExportableTrait schema.
     */
    private function buildExportConfig(): array
    {
        $raw = AdditionalExportConfigs::getInventoryItemConfig();

        // Map CSV config
        $csvHeaders = $raw['csv_headers'] ?? ['Name', 'Category', 'Type', 'Serial Number', 'Purchase Date', 'Warranty Expiry', 'Status'];
        $csvFieldsMap = $raw['csv_fields'] ?? [];
        $csvFields = [];
        foreach ($csvHeaders as $label) {
            $spec = $csvFieldsMap[$label] ?? null;
            if ($spec === null) continue;
            // Support string path or array spec with field/transform/default
            $csvFields[] = $spec;
        }

        // Columns for PDF views
        $pdfColumns = $raw['pdf_columns'] ?? [
            ['key' => 'name', 'label' => 'Name'],
            ['key' => 'item_category', 'label' => 'Category'],
            ['key' => 'item_type', 'label' => 'Type'],
            ['key' => 'serial_number', 'label' => 'Serial Number'],
            ['key' => 'status', 'label' => 'Status'],
            ['key' => 'purchase_date', 'label' => 'Purchase Date'],
            ['key' => 'warranty_expiry', 'label' => 'Warranty Expiry'],
        ];

        return [
            'csv' => [
                'headers' => $csvHeaders,
                'fields' => $csvFields,
                'filename_prefix' => 'inventory_items',
            ],
            'pdf' => [
                'view' => 'pdf-layout',
                'document_title' => 'Inventory Items List',
                'filename_prefix' => 'inventory_items',
                'orientation' => 'landscape',
                'columns' => $pdfColumns,
            ],
            'all_records' => [
                'view' => 'pdf-layout',
                'document_title' => 'Inventory Items List',
                'filename_prefix' => 'inventory_items',
                'orientation' => 'landscape',
                'include_index' => true,
                'columns' => $pdfColumns,
            ],
            'current_page' => [
                'view' => 'pdf-layout',
                'document_title' => 'Inventory Items (Current View)',
                'filename_prefix' => 'inventory_items_current',
                'orientation' => 'landscape',
                'columns' => $pdfColumns,
            ],
            'single_record' => [
                'view' => 'pdf-layout',
                'document_title' => 'Inventory Item Details',
                'filename_prefix' => 'inventory_item',
                // columns optional for single; the Blade can render fields of the model
            ],
        ];
    }
}

