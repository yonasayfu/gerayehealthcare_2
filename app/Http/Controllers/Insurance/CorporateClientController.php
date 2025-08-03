<?php

namespace App\Http\Controllers\Insurance;

use App\Http\Controllers\Controller;
use App\Http\Traits\ExportableTrait;
use App\Http\Config\AdditionalExportConfigs;
use App\Models\CorporateClient;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Inertia\Inertia;
use App\Http\Requests\StoreCorporateClientRequest;
use App\Http\Requests\UpdateCorporateClientRequest;

class CorporateClientController extends Controller
{
    use ExportableTrait;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = CorporateClient::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('organization_name', 'ilike', "%{$search}%")
                  ->orWhere('contact_email', 'ilike', "%{$search}%");
        }

        if ($request->filled('sort') && !empty($request->input('sort'))) {
            $query->orderBy($request->input('sort'), $request->input('direction', 'asc'));
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $corporateClients = $query->paginate($request->input('per_page', 5))->withQueryString();

        return Inertia::render('Insurance/CorporateClients/Index', [
            'corporateClients' => $corporateClients,
            'filters' => $request->only(['search', 'sort', 'direction', 'per_page']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Insurance/CorporateClients/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCorporateClientRequest $request)
    {
        $validated = $request->validated();

        CorporateClient::create($validated);

        return Redirect::route('admin.corporate-clients.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $corporateClient = CorporateClient::findOrFail($id);
        return Inertia::render('Insurance/CorporateClients/Show', [
            'corporateClient' => $corporateClient,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $corporateClient = CorporateClient::findOrFail($id);
        return Inertia::render('Insurance/CorporateClients/Edit', [
            'corporateClient' => $corporateClient,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCorporateClientRequest $request, string $id)
    {
        $corporateClient = CorporateClient::findOrFail($id);

        $validated = $request->validated();

        $corporateClient->update($validated);

        return Redirect::route('admin.corporate-clients.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $corporateClient = CorporateClient::findOrFail($id);

        $corporateClient->delete();

        return Redirect::route('admin.corporate-clients.index');
    }

    public function export(Request $request)
    {
        return $this->handleExport($request, CorporateClient::class, AdditionalExportConfigs::getCorporateClientConfig());
    }

    public function printSingle(CorporateClient $corporateClient)
    {
        return $this->handlePrintSingle($corporateClient, AdditionalExportConfigs::getCorporateClientConfig());
    }

    public function printCurrent(Request $request)
    {
        return $this->handlePrintCurrent($request, CorporateClient::class, AdditionalExportConfigs::getCorporateClientConfig());
    }

    public function printAll(Request $request)
    {
        return $this->handlePrintAll($request, CorporateClient::class, AdditionalExportConfigs::getCorporateClientConfig());
    }
}
