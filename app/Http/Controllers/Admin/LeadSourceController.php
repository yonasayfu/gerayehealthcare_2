<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Base\BaseController;
use App\Services\LeadSourceService;
use App\Models\LeadSource;
use App\Services\Validation\Rules\LeadSourceRules;
use Illuminate\Http\Request;
use Inertia\Inertia;
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
        return back()->with('success', 'Status updated successfully.');
    }

    /**
     * Temporary category source; replace with table-driven categories if needed.
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
