<?php

namespace App\Http\Controllers\Admin;

use App\DTOs\CreatePartnerCommissionDTO;
use App\Http\Controllers\Base\BaseController;
use App\Models\PartnerCommission;
use App\Services\CachedDropdown\CachedDropdownService;
use App\Services\PartnerCommission\PartnerCommissionService;
use App\Services\Validation\Rules\PartnerCommissionRules;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PartnerCommissionController extends BaseController
{
    public function __construct(PartnerCommissionService $partnerCommissionService)
    {
        parent::__construct(
            $partnerCommissionService,
            PartnerCommissionRules::class,
            'Admin/PartnerCommissions',
            'partnerCommissions',
            PartnerCommission::class,
            CreatePartnerCommissionDTO::class
        );
    }

    public function index(Request $request)
    {
        $data = $this->service->getAll($request, ['agreement', 'referral', 'invoice']);

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
        $partnerCommission = $this->service->getById($id, ['agreement', 'referral', 'invoice']);

        return Inertia::render('Admin/PartnerCommissions/Show', [
            'partnerCommission' => $partnerCommission,
        ]);
    }

    public function create()
    {
        // OPTIMIZED: Use cached dropdown service
        $partnerAgreements = CachedDropdownService::getPartnerAgreements();
        $referrals = CachedDropdownService::getReferrals();
        $invoices = CachedDropdownService::getInvoices();

        return Inertia::render('Admin/PartnerCommissions/Create', [
            'partnerAgreements' => $partnerAgreements->map(function ($agreement) {
                return [
                    'id' => $agreement->id,
                    'title' => $agreement->agreement_title,
                ];
            }),
            'referrals' => $referrals->map(function ($referral) {
                return [
                    'id' => $referral->id,
                    'referral_date' => $referral->referral_date,
                ];
            }),
            'invoices' => $invoices->map(function ($invoice) {
                return [
                    'id' => $invoice->id,
                    'invoice_number' => $invoice->invoice_number,
                ];
            }),
        ]);
    }

    public function edit($id)
    {
        $partnerCommission = $this->service->getById($id, ['agreement', 'referral', 'invoice']);

        // OPTIMIZED: Use cached dropdown service
        $partnerAgreements = CachedDropdownService::getPartnerAgreements();
        $referrals = CachedDropdownService::getReferrals();
        $invoices = CachedDropdownService::getInvoices();

        return Inertia::render('Admin/PartnerCommissions/Edit', [
            'partnerCommission' => $partnerCommission,
            'partnerAgreements' => $partnerAgreements->map(function ($agreement) {
                return [
                    'id' => $agreement->id,
                    'title' => $agreement->agreement_title,
                ];
            }),
            'referrals' => $referrals->map(function ($referral) {
                return [
                    'id' => $referral->id,
                    'referral_date' => $referral->referral_date,
                ];
            }),
            'invoices' => $invoices->map(function ($invoice) {
                return [
                    'id' => $invoice->id,
                    'invoice_number' => $invoice->invoice_number,
                ];
            }),
        ]);
    }

    public function export(Request $request)
    {
        return $this->service->export($request);
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
