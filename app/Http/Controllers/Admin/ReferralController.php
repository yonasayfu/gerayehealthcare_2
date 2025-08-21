<?php

namespace App\Http\Controllers\Admin;

use App\DTOs\CreateReferralDTO;
use App\Http\Controllers\Base\BaseController;
use App\Models\Partner;
use App\Models\PartnerAgreement;
use App\Models\Patient;
use App\Models\Referral;
use App\Services\Referral\ReferralService;
use App\Services\Validation\Rules\ReferralRules;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ReferralController extends BaseController
{
    public function __construct(ReferralService $referralService)
    {
        parent::__construct(
            $referralService,
            ReferralRules::class,
            'Admin/Referrals',
            'referrals',
            Referral::class,
            CreateReferralDTO::class
        );
    }

    public function index(Request $request)
    {
        $data = $this->service->getAll($request, ['partner', 'agreement', 'patient']);

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
        return Inertia::render('Admin/Referrals/Create', [
            'partners' => Partner::all()->map(function ($partner) {
                return [
                    'id' => $partner->id,
                    'name' => $partner->name,
                ];
            }),
            'partnerAgreements' => PartnerAgreement::all()->map(function ($agreement) {
                return [
                    'id' => $agreement->id,
                    'title' => $agreement->agreement_title,
                ];
            }),
            'patients' => Patient::all()->map(function ($patient) {
                return [
                    'id' => $patient->id,
                    'name' => $patient->full_name,
                ];
            }),
        ]);
    }

    public function edit($id)
    {
        $referral = $this->service->getById($id, ['partner', 'agreement', 'patient']);

        return Inertia::render('Admin/Referrals/Edit', [
            'referral' => $referral,
            'partners' => Partner::all()->map(function ($partner) {
                return [
                    'id' => $partner->id,
                    'name' => $partner->name,
                ];
            }),
            'partnerAgreements' => PartnerAgreement::all()->map(function ($agreement) {
                return [
                    'id' => $agreement->id,
                    'title' => $agreement->agreement_title,
                ];
            }),
            'patients' => Patient::all()->map(function ($patient) {
                return [
                    'id' => $patient->id,
                    'name' => $patient->full_name,
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
