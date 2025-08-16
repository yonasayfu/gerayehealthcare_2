<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Base\BaseController;
use App\Services\LeadSourceService;
use App\Models\LeadSource;
use App\Services\Validation\Rules\LeadSourceRules;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\DTOs\CreateLeadSourceDTO;
use App\DTOs\UpdateLeadSourceDTO;

class LeadSourceController extends BaseController
{
    public function __construct(LeadSourceService $leadSourceService)
    {
        parent::__construct(
            $leadSourceService,
            LeadSourceRules::class,
            'Admin/LeadSources',
            'leadSources',
            LeadSource::class,
            CreateLeadSourceDTO::class,
            UpdateLeadSourceDTO::class
        );
    }

    public function toggleStatus(Request $request, $id)
    {
        $this->service->toggleStatus($id);
        return back()->with('success', 'Status updated successfully.');
    }
}
