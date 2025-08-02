<?php

namespace App\Http\Controllers\Insurance;

use App\Http\Controllers\Controller;
use App\Http\Traits\ExportableTrait;
use App\Http\Config\AdditionalExportConfigs;
use App\Models\InsurancePolicy;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Inertia\Inertia;

class InsurancePolicyController extends Controller
{
    use ExportableTrait;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = InsurancePolicy::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('service_type', 'ilike', "%{$search}%")
                  ->orWhere('coverage_type', 'ilike', "%{$search}%");
        }

        if ($request->filled('sort') && !empty($request->input('sort'))) {
            $query->orderBy($request->input('sort'), $request->input('direction', 'asc'));
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $insurancePolicies = $query->paginate($request->input('per_page', 5))->withQueryString();

        return Inertia::render('Insurance/Policies/Index', [
            'insurancePolicies' => $insurancePolicies,
            'filters' => $request->only(['search', 'sort', 'direction', 'per_page']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Insurance/Policies/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'insurance_company_id' => 'required|exists:insurance_companies,id',
            'corporate_client_id' => 'required|exists:corporate_clients,id',
            'service_type' => 'nullable|string|max:255',
            'service_type_amharic' => 'nullable|string|max:255',
            'coverage_percentage' => 'required|numeric|min:0|max:100',
            'coverage_type' => 'required|string|max:255',
            'is_active' => 'required|boolean',
            'notes' => 'nullable|string',
        ]);

        InsurancePolicy::create($validated);

        return Redirect::route('admin.insurance-policies.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $insurancePolicy = InsurancePolicy::findOrFail($id);
        return Inertia::render('Insurance/Policies/Show', [
            'insurancePolicy' => $insurancePolicy,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $insurancePolicy = InsurancePolicy::findOrFail($id);
        return Inertia::render('Insurance/Policies/Edit', [
            'insurancePolicy' => $insurancePolicy,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $insurancePolicy = InsurancePolicy::findOrFail($id);

        $validated = $request->validate([
            'insurance_company_id' => 'required|exists:insurance_companies,id',
            'corporate_client_id' => 'required|exists:corporate_clients,id',
            'service_type' => 'nullable|string|max:255',
            'service_type_amharic' => 'nullable|string|max:255',
            'coverage_percentage' => 'required|numeric|min:0|max:100',
            'coverage_type' => 'required|string|max:255',
            'is_active' => 'required|boolean',
            'notes' => 'nullable|string',
        ]);

        $insurancePolicy->update($validated);

        return Redirect::route('admin.insurance-policies.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $insurancePolicy = InsurancePolicy::findOrFail($id);

        $insurancePolicy->delete();

        return Redirect::route('admin.insurance-policies.index');
    }

    public function export(Request $request)
    {
        return $this->handleExport($request, InsurancePolicy::class, AdditionalExportConfigs::getInsurancePolicyConfig());
    }

    public function printSingle(InsurancePolicy $insurancePolicy)
    {
        return $this->handlePrintSingle($insurancePolicy, AdditionalExportConfigs::getInsurancePolicyConfig());
    }

    public function printCurrent(Request $request)
    {
        return $this->handlePrintCurrent($request, InsurancePolicy::class, AdditionalExportConfigs::getInsurancePolicyConfig());
    }

    public function printAll(Request $request)
    {
        return $this->handlePrintAll($request, InsurancePolicy::class, AdditionalExportConfigs::getInsurancePolicyConfig());
    }
}
