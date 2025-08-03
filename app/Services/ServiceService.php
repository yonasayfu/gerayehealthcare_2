<?php

namespace App\Services;

use App\Models\Service;

class ServiceService extends BaseService
{
    public function __construct(Service $service)
    {
        parent::__construct($service);
    }

    protected function applySearch($query, $search)
    {
        $query->where('name', 'ilike', "%{$search}%");
    }
}
