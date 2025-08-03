<?php

namespace App\Http\Controllers\Insurance;

use App\Http\Controllers\Controller;
use App\Http\Traits\ExportableTrait;
use App\Http\Config\AdditionalExportConfigs;
use App\Models\InsuranceCompany;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Inertia\Inertia;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Requests\StoreInsuranceCompanyRequest;
use App\Http\Requests\UpdateInsuranceCompanyRequest;

class InsuranceCompanyController extends Controller
{
    use ExportableTrait;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = InsuranceCompany::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('name', 'ilike', "%{$search}%")
                  ->orWhere('contact_email', 'ilike', "%{$search}%");
        }

        if ($request->filled('sort') && !empty($request->input('sort'))) {
            $query->orderBy($request->input('sort'), $request->input('direction', 'asc'));
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $insuranceCompanies = $query->paginate($request->input('per_page', 5))->withQueryString();

        return Inertia::render('Insurance/Companies/Index', [
            'insuranceCompanies' => $insuranceCompanies,
            'filters' => $request->only(['search', 'sort', 'direction', 'per_page']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Insurance/Companies/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreInsuranceCompanyRequest $request)
    {
        $validated = $request->validated();

        InsuranceCompany::create($validated);

        return Redirect::route('admin.insurance-companies.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $insuranceCompany = InsuranceCompany::findOrFail($id);
        return Inertia::render('Insurance/Companies/Show', [
            'insuranceCompany' => $insuranceCompany,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $insuranceCompany = InsuranceCompany::findOrFail($id);
        return Inertia::render('Insurance/Companies/Edit', [
            'insuranceCompany' => $insuranceCompany,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateInsuranceCompanyRequest $request, string $id)
    {
        $insuranceCompany = InsuranceCompany::findOrFail($id);

        $validated = $request->validated();

        $insuranceCompany->update($validated);

        return Redirect::route('admin.insurance-companies.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $insuranceCompany = InsuranceCompany::findOrFail($id);

        $insuranceCompany->delete();

        return Redirect::route('admin.insurance-companies.index');
    }

    public function export(Request $request)
    {
        return $this->handleExport($request, InsuranceCompany::class, AdditionalExportConfigs::getInsuranceCompanyConfig());
    }

    public function printSingle(InsuranceCompany $insuranceCompany)
    {
        return $this->handlePrintSingle($insuranceCompany, AdditionalExportConfigs::getInsuranceCompanyConfig());
    }

    public function printCurrent(Request $request)
    {
        return $this->handlePrintCurrent($request, InsuranceCompany::class, AdditionalExportConfigs::getInsuranceCompanyConfig());
    }

    public function printAll(Request $request)
    {
        return $this->handlePrintAll($request, InsuranceCompany::class, AdditionalExportConfigs::getInsuranceCompanyConfig());
    }
}
