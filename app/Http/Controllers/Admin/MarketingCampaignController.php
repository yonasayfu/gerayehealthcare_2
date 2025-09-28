<?php

namespace App\Http\Controllers\Admin;

use App\DTOs\CreateMarketingCampaignDTO;
use App\DTOs\UpdateMarketingCampaignDTO;
use App\Http\Controllers\Base\BaseController;
use App\Http\Resources\MarketingCampaignResource;
use App\Models\MarketingCampaign;
use App\Models\MarketingPlatform;
use App\Models\Staff;
use App\Services\MarketingCampaign\MarketingCampaignService;
use App\Services\Validation\Rules\MarketingCampaignRules;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class MarketingCampaignController extends BaseController
{
    public function __construct(MarketingCampaignService $marketingCampaignService)
    {
        parent::__construct(
            $marketingCampaignService,
            MarketingCampaignRules::class,
            'Admin/MarketingCampaigns',
            'marketingCampaigns',
            MarketingCampaign::class,
            CreateMarketingCampaignDTO::class,
            MarketingCampaignResource::class
        );
    }

    public function index(Request $request)
    {
        $data = $this->service->getAll($request, [
            'platform', 
            'assignedStaff', 
            'responsibleStaff', 
            'createdByStaff'
        ]);

        return Inertia::render($this->viewName.'/Index', [
            $this->dataVariableName => $this->resourceClass::collection($data),
            'filters' => $request->only([
                'search', 'sort', 'direction', 'per_page',
                'sort_by', 'sort_order', 'active_only',
                'campaign_id', 'platform_id', 'status', 'period_start', 'period_end',
                'is_active', 'language',
            ]),
        ]);
    }

    public function show($id)
    {
        try {
            $data = $this->service->getById($id, [
                'platform', 
                'assignedStaff', 
                'responsibleStaff', 
                'createdByStaff',
                'contents',
                'metrics',
                'landingPages',
                'leads',
                'budgets',
                'tasks'
            ]);

            return Inertia::render($this->viewName.'/Show', [
                'marketingCampaign' => new $this->resourceClass($data),
            ]);
        } catch (\Exception $e) {
            Log::error('Error in MarketingCampaignController show method: '.$e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function create()
    {
        $platforms = MarketingPlatform::query()->select('id', 'name')->orderBy('name')->get();
        $staffMembers = Staff::query()->select('id')
            ->selectRaw("CONCAT(first_name, ' ', last_name) as full_name")
            ->orderBy('first_name')
            ->get();

        $campaignTypes = ['Awareness', 'Lead Gen', 'Conversion'];
        $statuses = ['Draft', 'Active', 'Paused', 'Completed'];

        return Inertia::render('Admin/MarketingCampaigns/Create', [
            'platforms' => $platforms,
            'staffMembers' => $staffMembers,
            'campaignTypes' => $campaignTypes,
            'statuses' => $statuses,
        ]);
    }

    public function printAll()
    {
        return app(MarketingCampaignService::class)->printAll(request());
    }

    public function printCurrent(Request $request)
    {
        return app(MarketingCampaignService::class)->printCurrent($request);
    }

    public function printSingle(Request $request, MarketingCampaign $marketing_campaign)
    {
        return app(MarketingCampaignService::class)->printSingle($request, $marketing_campaign);
    }

    /**
     * Override edit to provide select option lists to the Edit view.
     */
    public function edit($id)
    {
        $data = $this->service->getById($id);

        $platforms = MarketingPlatform::query()->select('id', 'name')->orderBy('name')->get();
        $staffMembers = Staff::query()->select('id')
            ->selectRaw("CONCAT(first_name, ' ', last_name) as full_name")
            ->orderBy('first_name')
            ->get();

        $campaignTypes = ['Awareness', 'Lead Gen', 'Conversion'];
        $statuses = ['Draft', 'Active', 'Paused', 'Completed'];

        return Inertia::render('Admin/MarketingCampaigns/Edit', [
            'marketingCampaign' => new MarketingCampaignResource($data),
            'platforms' => $platforms,
            'staffMembers' => $staffMembers,
            'campaignTypes' => $campaignTypes,
            'statuses' => $statuses,
        ]);
    }

    /**
     * Override update to use UpdateMarketingCampaignDTO and avoid nulling campaign_code.
     */
    public function update(Request $request, $id)
    {
        try {
            $model = $this->service->getById($id);
            $validatedData = $this->validateRequest($request, 'update', $model);

            // Instantiate Update DTO explicitly
            $dto = new UpdateMarketingCampaignDTO(
                campaign_name: $validatedData['campaign_name'],
                campaign_code: $model->campaign_code, // keep existing code
                utm_campaign: $validatedData['utm_campaign'] ?? null,
                platform_id: $validatedData['platform_id'] ?? null,
                campaign_type: $validatedData['campaign_type'] ?? null,
                status: $validatedData['status'] ?? null,
                start_date: $validatedData['start_date'] ?? null,
                end_date: $validatedData['end_date'] ?? null,
                description: $validatedData['description'] ?? null,
                assigned_staff_id: $validatedData['assigned_staff_id'] ?? null,
                created_by_staff_id: null,
                urgency: $validatedData['urgency'] ?? null,
                responsible_staff_id: $validatedData['responsible_staff_id'] ?? null,
            );

            $payload = method_exists($dto, 'toArray') ? $dto->toArray() : (array) $dto;

            // Ensure campaign_code is not unintentionally updated to null
            $payload['campaign_code'] = $model->campaign_code;

            $this->service->update($id, $payload);

            return redirect()->route('admin.'.'marketing-campaigns'.'.index')
                ->with('banner', ucfirst('marketingCampaigns').' updated successfully.')->with('bannerStyle', 'success');
        } catch (
Exception $e) {
            Log::error('Error in MarketingCampaignController update method: '.$e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
