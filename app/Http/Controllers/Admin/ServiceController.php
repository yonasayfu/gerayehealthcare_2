<?php

namespace App\Http\Controllers\Admin;

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
            Service::class
        );
    }

    public function show(Service $service)
    {
        return parent::show($service->id);
    }

    public function edit(Service $service)
    {
        return parent::edit($service->id);
    }

    public function update(Request $request, Service $service)
    {
        return parent::update($request, $service->id);
    }

    public function destroy(Service $service)
    {
        return parent::destroy($service->id);
    }
}
