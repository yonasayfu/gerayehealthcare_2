<?php

namespace App\Http\Controllers\Admin;

use App\DTOs\CreatePartnerAgreementDTO;
use App\Http\Controllers\Base\BaseController;
use App\Models\Partner;
use App\Models\PartnerAgreement;
use App\Models\Staff;
use App\Services\PartnerAgreement\PartnerAgreementService;
use App\Services\Validation\Rules\PartnerAgreementRules;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PartnerAgreementController extends BaseController
{
    public function __construct(PartnerAgreementService $partnerAgreementService)
    {
        parent::__construct(
            $partnerAgreementService,
            PartnerAgreementRules::class,
            'Admin/PartnerAgreements',
            'partnerAgreements',
            PartnerAgreement::class,
            CreatePartnerAgreementDTO::class
        );
    }

    public function index(Request $request)
    {
        $data = $this->service->getAll($request, ['partner', 'signedBy']);

        return Inertia::render($this->viewName.'/Index', [
            $this->dataVariableName => $data,
            'filters' => $request->only([
                'search', 'sort', 'direction', 'per_page',
                'sort_by', 'sort_order', 'active_only',
                'campaign_id', 'platform_id', 'status', 'period_start', 'period_end',
                'is_active', 'language',
            ]),
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/PartnerAgreements/Create', [
            'partners' => Partner::all()->map(function ($partner) {
                return [
                    'id' => $partner->id,
                    'name' => $partner->name,
                ];
            }),
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
        $partnerAgreement = $this->service->getById($id, ['partner', 'signedBy']);

        return Inertia::render('Admin/PartnerAgreements/Edit', [
            'partnerAgreement' => $partnerAgreement,
            'partners' => Partner::all()->map(function ($partner) {
                return [
                    'id' => $partner->id,
                    'name' => $partner->name,
                ];
            }),
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
