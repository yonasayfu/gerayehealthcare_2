<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\ExportableTrait;
use App\Http\Traits\IndexableTrait;
use App\Http\Config\AdditionalExportConfigs;
use App\Models\EligibilityCriteria;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Response;
use App\Http\Requests\StoreEligibilityCriteriaRequest;
use App\Http\Requests\UpdateEligibilityCriteriaRequest;

class EligibilityCriteriaController extends Controller
{
    use ExportableTrait, IndexableTrait;
    
    public function __construct()
    {
        $this->middleware('role:' . \App\Enums\RoleEnum::SUPER_ADMIN->value . '|' . \App\Enums\RoleEnum::ADMIN->value);
    }
    
    /**
     * Get the model class for the controller.
     *
     * @return string
     */
    protected function getModelClass()
    {
        return EligibilityCriteria::class;
    }
    
    /**
     * Get the view name for the controller.
     *
     * @return string
     */
    protected function getViewName()
    {
        return 'Admin/EligibilityCriteria/Index';
    }
    
    /**
     * Get the data variable name for the controller.
     *
     * @return string
     */
    protected function getDataVariableName()
    {
        return 'criteria';
    }
    
    /**
     * Get the sortable fields for the controller.
     *
     * @return array
     */
    protected function getSortableFields()
    {
        return ['criteria_name', 'operator', 'value', 'created_at'];
    }
    
    /**
     * Apply search to the query.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $search
     * @return void
     */
    protected function applySearch($query, $search)
    {
        $query->where('criteria_name', 'ilike', "%{$search}%");
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Admin/EligibilityCriteria/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEligibilityCriteriaRequest $request)
    {
        $validated = $request->validated();

        EligibilityCriteria::create($validated);

        return redirect()->route('admin.eligibility-criteria.index')
            ->with('success', 'Eligibility criteria created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(EligibilityCriteria $eligibilityCriteria)
    {
        return Inertia::render('Admin/EligibilityCriteria/Show', [
            'eligibilityCriteria' => $eligibilityCriteria,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(EligibilityCriteria $eligibilityCriteria)
    {
        return Inertia::render('Admin/EligibilityCriteria/Edit', [
            'eligibilityCriteria' => $eligibilityCriteria,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEligibilityCriteriaRequest $request, EligibilityCriteria $eligibilityCriteria)
    {
        $validated = $request->validated();

        $eligibilityCriteria->update($validated);

        return redirect()->route('admin.eligibility-criteria.index')
            ->with('success', 'Eligibility criteria updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(EligibilityCriteria $eligibilityCriteria)
    {
        $eligibilityCriteria->delete();

        return redirect()->route('admin.eligibility-criteria.index')
            ->with('success', 'Eligibility criteria deleted successfully.');
    }

    public function export(Request $request)
    {
        return $this->handleExport($request, EligibilityCriteria::class, AdditionalExportConfigs::getEligibilityCriteriaConfig());
    }

    public function printSingle(EligibilityCriteria $eligibilityCriteria)
    {
        return $this->handlePrintSingle($eligibilityCriteria, AdditionalExportConfigs::getEligibilityCriteriaConfig());
    }

    public function printCurrent(Request $request)
    {
        return $this->handlePrintCurrent($request, EligibilityCriteria::class, AdditionalExportConfigs::getEligibilityCriteriaConfig());
    }

    public function printAll(Request $request)
    {
        return $this->handlePrintAll($request, EligibilityCriteria::class, AdditionalExportConfigs::getEligibilityCriteriaConfig());
    }
}
