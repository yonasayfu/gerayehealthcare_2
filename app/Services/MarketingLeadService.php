<?php

namespace App\Services;

use App\DTOs\CreateMarketingLeadDTO;
use App\Models\MarketingLead;
use Illuminate\Http\Request;

class MarketingLeadService extends BaseService
{
    public function __construct(MarketingLead $marketingLead)
    {
        parent::__construct($marketingLead);
    }

    protected function applySearch($query, $search)
    {
        return $query->where('first_name', 'ilike', "%{$search}%")
                  ->orWhere('last_name', 'ilike', "%{$search}%")
                  ->orWhere('email', 'ilike', "%{$search}%")
                  ->orWhere('lead_code', 'ilike', "%{$search}%");
    }

    public function getAll(Request $request, array $with = [])
    {
        $query = $this->model->with(array_merge(['sourceCampaign', 'landingPage', 'assignedStaff', 'convertedPatient'], $with));

        if ($request->has('search')) {
            $this->applySearch($query, $request->input('search'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }
        if ($request->filled('source_campaign_id')) {
            $query->where('source_campaign_id', $request->input('source_campaign_id'));
        }
        if ($request->filled('landing_page_id')) {
            $query->where('landing_page_id', $request->input('landing_page_id'));
        }
        if ($request->filled('assigned_staff_id')) {
            $query->where('assigned_staff_id', $request->input('assigned_staff_id'));
        }

        if ($request->has('sort')) {
            $direction = $request->input('direction', 'asc');
            $query->orderBy($request->input('sort'), $direction);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        return $query->paginate($request->input('per_page', 5));
    }

    public function update(int $id, array|object $data): MarketingLead
    {
        $data = is_object($data) ? (array) $data : $data;

        // Prevent lead_code from being updated
        if (isset($data['lead_code'])) {
            unset($data['lead_code']);
        }

        return parent::update($id, $data);
    }

    /**
     * Ensure single record fetch includes related entities for UI Show/Edit pages.
     */
    public function getById(int $id, array $with = [])
    {
        $relations = array_merge(['sourceCampaign', 'landingPage', 'assignedStaff', 'convertedPatient'], $with);
        return parent::getById($id, $relations);
    }
    
}
