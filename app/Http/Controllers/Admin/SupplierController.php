<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Base\BaseController;
use App\Services\SupplierService;
use App\Models\Supplier;
use App\Services\Validation\Rules\SupplierRules;
use Inertia\Inertia;

class SupplierController extends BaseController
{
    public function __construct(SupplierService $supplierService)
    {
        parent::__construct(
            $supplierService,
            SupplierRules::class,
            'Admin/Suppliers',
            'suppliers',
            Supplier::class
        );
    }

    public function show(Supplier $supplier)
    {
        return parent::show($supplier->id);
    }

    public function edit(Supplier $supplier)
    {
        return parent::edit($supplier->id);
    }

    public function update(Request $request, Supplier $supplier)
    {
        return parent::update($request, $supplier->id);
    }

    public function destroy(Supplier $supplier)
    {
        return parent::destroy($supplier->id);
    }

    public function printSingle(Supplier $supplier)
    {
        return parent::printSingle($supplier->id);
    }

    public function createImport()
    {
        return Inertia::render('Admin/Suppliers/Import');
    }

    public function storeImport(Request $request)
    {
        $errors = $this->service->import($request);

        if (count($errors) > 0) {
            return redirect()->back()->withErrors($errors);
        }

        return redirect()->route('admin.suppliers.index')->with('success', 'Suppliers imported successfully.');
    }
}