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
            Supplier::class,
            CreateSupplierDTO::class
        );
    }

   
}