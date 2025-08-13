<?php

namespace App\Services;

use App\Models\Supplier;
use Illuminate\Http\Request;
use App\Http\Traits\ExportableTrait;
use App\Http\Config\AdditionalExportConfigs;
use Illuminate\Support\Facades\Validator;

class SupplierService extends BaseService
{
    use ExportableTrait;

    public function __construct(Supplier $supplier)
    {
        parent::__construct($supplier);
    }

    protected function applySearch($query, $search)
    {
        $query->where('name', 'ilike', "%{$search}%")
              ->orWhere('contact_person', 'ilike', "%{$search}%")
              ->orWhere('email', 'ilike', "%{$search}%")
              ->orWhere('phone', 'ilike', "%{$search}%");
    }

    
}
