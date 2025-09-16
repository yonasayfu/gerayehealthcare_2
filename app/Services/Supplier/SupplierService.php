<?php

namespace App\Services\Supplier;

use App\Models\Supplier;
use App\Services\Base\BaseService;

class SupplierService extends BaseService
{
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
