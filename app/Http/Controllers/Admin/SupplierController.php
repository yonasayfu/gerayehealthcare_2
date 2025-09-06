<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Base\BaseController;
use App\Http\Traits\ExportableTrait;
use App\Models\Supplier;
use App\Services\SupplierService;
use App\Services\Validation\Rules\SupplierRules;
use Illuminate\Http\Request;

class SupplierController extends BaseController
{
    use ExportableTrait;

    public function __construct(SupplierService $supplierService)
    {
        parent::__construct(
            $supplierService,
            SupplierRules::class,
            'Admin/Suppliers',
            'suppliers',
            Supplier::class,
            'App\\DTOs\\CreateSupplierDTO'
        );
    }

    /**
     * Print all suppliers (PDF) with current filters applied.
     */
    public function printAll(Request $request)
    {
        $config = $this->buildExportConfig();

        return $this->handlePrintAll($request, Supplier::class, $config);
    }

    /**
     * Print a single supplier (PDF).
     */
    public function printSingle(Request $request, $id)
    {
        $model = Supplier::findOrFail($id);
        $config = $this->buildExportConfig();

        return $this->handlePrintSingle($request, $model, $config);
    }

    /**
     * Export/print configuration for Suppliers.
     */
    private function buildExportConfig(): array
    {
        $pdfColumns = [
            ['key' => 'name', 'label' => 'Name'],
            ['key' => 'contact_person', 'label' => 'Contact Person'],
            ['key' => 'email', 'label' => 'Email'],
            ['key' => 'phone', 'label' => 'Phone'],
            ['key' => 'address', 'label' => 'Address'],
        ];

        return [
            'pdf' => [
                'view' => 'pdf-layout',
                'document_title' => 'Suppliers',
                'filename_prefix' => 'suppliers',
                'orientation' => 'portrait',
                'columns' => $pdfColumns,
            ],
            'all_records' => [
                'view' => 'pdf-layout',
                'document_title' => 'Suppliers List',
                'filename_prefix' => 'suppliers',
                'orientation' => 'landscape',
                'include_index' => true,
                'columns' => $pdfColumns,
            ],
            'single_record' => [
                'view' => 'pdf-layout',
                'document_title' => 'Supplier Details',
                'filename_prefix' => 'supplier',
            ],
        ];
    }
}
