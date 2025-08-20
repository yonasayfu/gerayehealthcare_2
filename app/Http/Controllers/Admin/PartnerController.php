<?php

namespace App\Http\Controllers\Admin;

use App\DTOs\CreatePartnerDTO;
use App\Http\Controllers\Base\BaseController;
use App\Models\Partner;
use App\Models\Staff;
use App\Services\Partner\PartnerService;
use App\Services\Validation\Rules\PartnerRules;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PartnerController extends BaseController
{
    public function __construct(PartnerService $partnerService)
    {
        parent::__construct(
            $partnerService,
            PartnerRules::class,
            'Admin/Partners',
            'partners',
            Partner::class,
            CreatePartnerDTO::class
        );
    }

    public function create()
    {
        return Inertia::render('Admin/Partners/Create', [
            'staff' => Staff::all()->map(function ($staff) {
                return [
                    'id' => $staff->id,
                    'name' => $staff->first_name.' '.$staff->last_name,
                ];
            }),
        ]);
    }

    public function edit($id)
    {
        $partner = $this->service->getById($id);

        return Inertia::render('Admin/Partners/Edit', [
            'partner' => $partner,
            'staff' => Staff::all()->map(function ($staff) {
                return [
                    'id' => $staff->id,
                    'name' => $staff->first_name.' '.$staff->last_name,
                ];
            }),
        ]);
    }

    public function export(Request $request)
    {
        return $this->service->export($request);
    }

    public function printAll(Request $request)
    {
        return $this->service->printAll($request);
    }

    public function printCurrent(Request $request)
    {
        return $this->service->printCurrent($request);
    }

    public function printSingle(Request $request, $id)
    {
        return $this->service->printSingle($request, $id);
    }
}
