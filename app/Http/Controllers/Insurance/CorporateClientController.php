<?php

namespace App\Http\Controllers\Insurance;

use App\Http\Controllers\Controller;
use App\Models\CorporateClient;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Inertia\Inertia;

class CorporateClientController extends Controller
{
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

        $corporateClients = $query->paginate($request->input('per_page', 10))->withQueryString();

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
    public function store(Request $request)
    {
        $validated = $request->validate([
            'organization_name' => 'required|string|max:255',
            'organization_name_amharic' => 'nullable|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'contact_email' => 'nullable|email|max:255',
            'contact_phone' => 'nullable|string|max:255',
            'tin_number' => 'nullable|string|max:50',
            'trade_license_number' => 'nullable|string|max:100',
            'address' => 'nullable|string',
        ]);

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
    public function update(Request $request, string $id)
    {
        $corporateClient = CorporateClient::findOrFail($id);

        $validated = $request->validate([
            'organization_name' => 'required|string|max:255',
            'organization_name_amharic' => 'nullable|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'contact_email' => 'nullable|email|max:255',
            'contact_phone' => 'nullable|string|max:255',
            'tin_number' => 'nullable|string|max:50',
            'trade_license_number' => 'nullable|string|max:100',
            'address' => 'nullable|string',
        ]);

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
        $type = $request->get('type');
        $corporateClients = CorporateClient::select('organization_name', 'contact_person', 'contact_email', 'contact_phone', 'tin_number', 'trade_license_number', 'address')->get();

        if ($type === 'csv') {
            $csvData = "Organization Name,Contact Person,Contact Email,Contact Phone,TIN Number,Trade License Number,Address\n";
            foreach ($corporateClients as $client) {
                $csvData .= "\"{$client->organization_name}\",\"{$client->contact_person}\",\"{$client->contact_email}\",\"{$client->contact_phone}\",\"{$client->tin_number}\",\"{$client->trade_license_number}\",\"{$client->address}\"\n";
            }

            return Response::make($csvData, 200, [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="corporate_clients.csv"',
            ]);
        }

        if ($type === 'pdf') {
            $pdf = Pdf::loadView('pdf.corporate_clients', ['corporateClients' => $corporateClients])->setPaper('a4', 'landscape');
            return $pdf->stream('corporate_clients.pdf');
        }

        return abort(400, 'Invalid export type');
    }

    public function printSingle(CorporateClient $corporateClient)
    {
        $pdf = Pdf::loadView('pdf.corporate_client_single', ['corporateClient' => $corporateClient])->setPaper('a4', 'portrait');
        return $pdf->stream("corporate_client-{$corporateClient->id}.pdf");
    }

    public function printCurrent(Request $request)
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

        $corporateClients = $query->paginate($request->input('per_page', 10))->appends($request->except('page'));

        return Inertia::render('Insurance/CorporateClients/PrintCurrent', ['corporateClients' => $corporateClients->items()]);
    }

    public function printAll(Request $request)
    {
        $corporateClients = CorporateClient::orderBy('organization_name')->get();

        $pdf = Pdf::loadView('pdf.corporate_clients', ['corporateClients' => $corporateClients])->setPaper('a4', 'landscape');
        return $pdf->stream('corporate_clients.pdf');
    }
}
