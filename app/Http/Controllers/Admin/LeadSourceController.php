<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Base\BaseController;
use App\Services\LeadSourceService;
use App\Models\LeadSource;
use App\Services\Validation\Rules\LeadSourceRules;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LeadSourceController extends BaseController
{
    public function __construct(LeadSourceService $leadSourceService)
    {
        parent::__construct(
            $leadSourceService,
            LeadSourceRules::class,
            'Admin/LeadSources',
            'leadSources',
            LeadSource::class
        );
    }

    public function create()
    {
        $categories = ['Online', 'Offline', 'Referral', 'Event', 'Other'];
        return Inertia::render('Admin/LeadSources/Create', [
            'categories' => $categories,
        ]);
    }

    public function show(LeadSource $leadSource)
    {
        return parent::show($leadSource->id);
    }

    public function edit(LeadSource $leadSource)
    {
        $categories = ['Online', 'Offline', 'Referral', 'Event', 'Other'];
        return Inertia::render('Admin/LeadSources/Edit', [
            'leadSource' => $leadSource,
            'categories' => $categories,
        ]);
    }

    public function update(Request $request, LeadSource $leadSource)
    {
        return parent::update($request, $leadSource->id);
    }

    public function destroy(LeadSource $leadSource)
    {
        return parent::destroy($leadSource->id);
    }

    public function toggleStatus(LeadSource $leadSource)
    {
        $this->service->toggleStatus($leadSource);
        return back()->with('success', 'Lead Source status updated successfully.');
    }

    public function export(Request $request)
    {
        return parent::export($request);
    }

    public function printSingle(LeadSource $leadSource)
    {
        return parent::printSingle($leadSource->id);
    }
}