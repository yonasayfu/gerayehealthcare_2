<?php

namespace App\Http\Controllers\Admin;

use App\DTOs\CreatePartnerEngagementDTO;
use App\Http\Controllers\Base\BaseController;
use App\Models\PartnerEngagement;
use App\Services\CachedDropdownService;
use App\Services\PartnerEngagement\PartnerEngagementService;
use App\Services\Validation\Rules\PartnerEngagementRules;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PartnerEngagementController extends BaseController
{
    public function __construct(PartnerEngagementService $partnerEngagementService)
    {
        parent::__construct(
            $partnerEngagementService,
            PartnerEngagementRules::class,
            'Admin/PartnerEngagements',
            'partnerEngagements',
            PartnerEngagement::class,
            CreatePartnerEngagementDTO::class
        );
    }

    public function index(Request $request)
    {
        $data = $this->service->getAll($request, ['partner', 'staff']);

        return Inertia::render($this->viewName.'/Index', [
            $this->dataVariableName => $data,
            'filters' => $request->only([
                'search',
                'sort',
                'direction',
                'per_page',
                'sort_by',
                'sort_order',
                'active_only',
                'campaign_id',
                'platform_id',
                'status',
                'period_start',
                'period_end',
                'is_active',
                'language',
            ]),
        ]);
    }

    public function show($id)
    {
        // Ensure related Partner and Staff are loaded for the Show view
        $partnerEngagement = $this->service->getById($id, ['partner', 'staff']);

        return Inertia::render($this->viewName.'/Show', [
            'partnerEngagement' => $partnerEngagement,
        ]);
    }

    public function create()
    {
        // OPTIMIZED: Use cached dropdown service
        $partners = CachedDropdownService::getPartners();
        $staff = CachedDropdownService::getActiveStaff();

        return Inertia::render('Admin/PartnerEngagements/Create', [
            'partners' => $partners->map(function ($partner) {
                return [
                    'id' => $partner->id,
                    'name' => $partner->name,
                ];
            }),
            'staff' => $staff->map(function ($staffMember) {
                return [
                    'id' => $staffMember->id,
                    'name' => $staffMember->first_name.' '.$staffMember->last_name,
                ];
            }),
        ]);
    }

    public function edit($id)
    {
        $partnerEngagement = $this->service->getById($id, ['partner', 'staff']);

        // OPTIMIZED: Use cached dropdown service
        $partners = CachedDropdownService::getPartners();
        $staff = CachedDropdownService::getActiveStaff();

        return Inertia::render('Admin/PartnerEngagements/Edit', [
            'partnerEngagement' => $partnerEngagement,
            'partners' => $partners->map(function ($partner) {
                return [
                    'id' => $partner->id,
                    'name' => $partner->name,
                ];
            }),
            'staff' => $staff->map(function ($staffMember) {
                return [
                    'id' => $staffMember->id,
                    'name' => $staffMember->first_name.' '.$staffMember->last_name,
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
