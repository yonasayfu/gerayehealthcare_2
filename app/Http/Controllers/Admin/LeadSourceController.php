<?php

namespace App\Http\Controllers\Admin;

use App\DTOs\UpdateLeadSourceDTO;
use App\Http\Controllers\Base\BaseController;
use App\Models\LeadSource;
use App\Services\LeadSource\LeadSourceService;
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
            LeadSource::class,
            UpdateLeadSourceDTO::class
        );
    }

    /**
     * Provide categories to Create view
     */
    public function create()
    {
        $categories = $this->getCategories();

        return Inertia::render('Admin/LeadSources/Create', [
            'categories' => $categories,
        ]);
    }

    /**
     * Provide categories to Edit view along with the record
     */
    public function edit($id)
    {
        $data = $this->service->getById($id);
        $propName = lcfirst(class_basename($this->modelClass));
        $categories = $this->getCategories();

        return Inertia::render('Admin/LeadSources/Edit', [
            $propName => $data,
            'categories' => $categories,
        ]);
    }

    public function toggleStatus(Request $request, $id)
    {
        $this->service->toggleStatus($id);

        return back()->with('banner', 'Status updated successfully.')->with('bannerStyle', 'success');
    }

    /**
     * Temporary category source; replace with table-driven categories if needed.
     *
     * @return array<string>
     */
    private function getCategories(): array
    {
        return [
            'Website',
            'Referral',
            'Social Media',
            'Walk-in',
            'Phone Inquiry',
            'Email Campaign',
            'Other',
        ];
    }
}
