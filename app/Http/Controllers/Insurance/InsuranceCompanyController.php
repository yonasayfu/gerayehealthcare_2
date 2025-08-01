<?php

namespace App\Http\Controllers\Insurance;

use App\Http\Controllers\Controller;
use App\Models\InsuranceCompany;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Response;
use Inertia\Inertia;

class InsuranceCompanyController extends Controller
{
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
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_amharic' => 'nullable|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'contact_email' => 'nullable|email|max:255',
            'contact_phone' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'address_amharic' => 'nullable|string',
        ]);

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
    public function update(Request $request, string $id)
    {
        $insuranceCompany = InsuranceCompany::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_amharic' => 'nullable|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'contact_email' => 'nullable|email|max:255',
            'contact_phone' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'address_amharic' => 'nullable|string',
        ]);

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
        $type = $request->get('type');
        $insuranceCompanies = InsuranceCompany::select('name', 'contact_person', 'contact_email', 'contact_phone', 'address')->get();

        if ($type === 'csv') {
            $csvData = "Name,Contact Person,Contact Email,Contact Phone,Address\n";
            foreach ($insuranceCompanies as $company) {
                $csvData .= "\"{$company->name}\",\"{$company->contact_person}\",\"{$company->contact_email}\",\"{$company->contact_phone}\",\"{$company->address}\"\n";
            }

            return Response::make($csvData, 200, [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename=\"insurance_companies.csv\"',
            ]);
        }

        if ($type === 'pdf') {
            $pdf = Pdf::loadView('pdf.insurance_companies', ['insuranceCompanies' => $insuranceCompanies])->setPaper('a4', 'landscape');
            return $pdf->stream('insurance_companies.pdf');
        }

        return abort(400, 'Invalid export type');
    }

    public function printSingle(InsuranceCompany $insuranceCompany)
    {
        $pdf = Pdf::loadView('pdf.insurance_company_single', ['insuranceCompany' => $insuranceCompany])->setPaper('a4', 'portrait');
        return $pdf->stream("insurance_company-{$insuranceCompany->id}.pdf");
    }

    public function printCurrent(Request $request)
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

        $insuranceCompanies = $query->paginate($request->input('per_page', 5))->appends($request->except('page'));

        return Inertia::render('Insurance/Companies/PrintCurrent', ['insuranceCompanies' => $insuranceCompanies->items()]);
    }

    public function printAll(Request $request)
    {
        $insuranceCompanies = InsuranceCompany::orderBy('name')->get();

        $pdf = Pdf::loadView('pdf.insurance_companies', ['insuranceCompanies' => $insuranceCompanies])->setPaper('a4', 'landscape');
        return $pdf->stream('insurance_companies.pdf');
    }
}
