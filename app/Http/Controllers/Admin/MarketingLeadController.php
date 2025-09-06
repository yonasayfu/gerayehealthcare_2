<?php

namespace App\Http\Controllers\Admin;

use App\DTOs\CreateMarketingLeadDTO;
use App\DTOs\UpdateMarketingLeadDTO;
use App\Http\Controllers\Base\BaseController;
use App\Models\LandingPage;
use App\Models\MarketingCampaign;
use App\Models\MarketingLead;
use App\Models\Patient;
use App\Models\Staff;
use App\Services\MarketingLeadService;
use App\Services\Validation\Rules\MarketingLeadRules;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MarketingLeadController extends BaseController
{
    public function __construct(MarketingLeadService $marketingLeadService)
    {
        parent::__construct(
            $marketingLeadService,
            MarketingLeadRules::class,
            'Admin/MarketingLeads',
            'marketingLeads',
            MarketingLead::class,
            CreateMarketingLeadDTO::class
        );
    }

    public function create()
    {
        $campaigns = MarketingCampaign::select('id', 'campaign_name')->orderBy('campaign_name')->get();
        $landingPages = LandingPage::select('id', 'page_title')->orderBy('page_title')->get();
        $staffMembers = Staff::select('id', 'first_name', 'last_name')
            ->orderBy('first_name')
            ->get()
            ->map(fn ($s) => [
                'id' => $s->id,
                'full_name' => trim(($s->first_name ?? '').' '.($s->last_name ?? '')),
            ]);
        $patients = Patient::select('id', 'full_name')->orderBy('full_name')->get();

        $statuses = ['New', 'Contacted', 'Qualified', 'Disqualified', 'Converted'];
        $countries = [
            'Ethiopia', 'United States', 'United Kingdom', 'Canada', 'Germany', 'France', 'Italy', 'Spain', 'Kenya', 'South Africa',
            'UAE', 'Saudi Arabia', 'India', 'China', 'Japan', 'Australia', 'Netherlands', 'Sweden', 'Norway', 'Denmark',
        ];

        return Inertia::render($this->viewName.'/Create', [
            'campaigns' => $campaigns,
            'landingPages' => $landingPages,
            'staffMembers' => $staffMembers,
            'patients' => $patients,
            'statuses' => $statuses,
            'countries' => $countries,
        ]);
    }

    public function edit($id)
    {
        $marketingLead = $this->service->getById($id);
        $campaigns = MarketingCampaign::select('id', 'campaign_name')->orderBy('campaign_name')->get();
        $landingPages = LandingPage::select('id', 'page_title')->orderBy('page_title')->get();
        $staffMembers = Staff::select('id', 'first_name', 'last_name')
            ->orderBy('first_name')
            ->get()
            ->map(fn ($s) => [
                'id' => $s->id,
                'full_name' => trim(($s->first_name ?? '').' '.($s->last_name ?? '')),
            ]);
        $patients = Patient::select('id', 'full_name')->orderBy('full_name')->get();

        $statuses = ['New', 'Contacted', 'Qualified', 'Disqualified', 'Converted'];
        $countries = [
            'Ethiopia', 'United States', 'United Kingdom', 'Canada', 'Germany', 'France', 'Italy', 'Spain', 'Kenya', 'South Africa',
            'UAE', 'Saudi Arabia', 'India', 'China', 'Japan', 'Australia', 'Netherlands', 'Sweden', 'Norway', 'Denmark',
        ];

        return Inertia::render($this->viewName.'/Edit', [
            lcfirst(class_basename($this->modelClass)) => $marketingLead,
            'campaigns' => $campaigns,
            'landingPages' => $landingPages,
            'staffMembers' => $staffMembers,
            'patients' => $patients,
            'statuses' => $statuses,
            'countries' => $countries,
        ]);
    }

    public function update(Request $request, $id)
    {
        // validate using rules class
        $model = $this->service->getById($id);
        $validatedData = $request->validate(MarketingLeadRules::update($model));

        // Build Update DTO explicitly to ensure lead_code is never included
        $dto = new UpdateMarketingLeadDTO(
            $validatedData['first_name'] ?? $model->first_name,
            $validatedData['last_name'] ?? $model->last_name,
            $validatedData['email'] ?? $model->email,
            $validatedData['phone'] ?? $model->phone,
            $validatedData['country'] ?? $model->country,
            $validatedData['utm_source'] ?? $model->utm_source,
            $validatedData['utm_campaign'] ?? $model->utm_campaign,
            $validatedData['utm_medium'] ?? $model->utm_medium,
            $validatedData['landing_page_id'] ?? $model->landing_page_id,
            $validatedData['lead_score'] ?? $model->lead_score,
            $validatedData['status'] ?? $model->status,
            $validatedData['assigned_staff_id'] ?? $model->assigned_staff_id,
            $validatedData['converted_patient_id'] ?? $model->converted_patient_id,
            $validatedData['conversion_date'] ?? ($model->conversion_date?->format('Y-m-d') ?? null),
            $validatedData['notes'] ?? $model->notes,
        );

        $this->service->update($id, method_exists($dto, 'toArray') ? $dto->toArray() : (array) $dto);

        return redirect()->route('admin.marketing-leads.index')->with('banner', 'Marketing lead updated successfully.')->with('bannerStyle', 'success');
    }
}
