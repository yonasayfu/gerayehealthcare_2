<?php

namespace App\Http\Controllers\Admin;

use App\DTOs\CreateServiceDTO;
use App\Http\Controllers\Base\BaseController;
use App\Services\ServiceService;
use App\Models\Service;
use App\Services\Validation\Rules\ServiceRules;

class ServiceController extends BaseController
{
    public function __construct(ServiceService $serviceService)
    {
        parent::__construct(
            $serviceService,
            ServiceRules::class,
            'Admin/Services',
            'services',
            Service::class,
            CreateServiceDTO::class
        );
    }
}
