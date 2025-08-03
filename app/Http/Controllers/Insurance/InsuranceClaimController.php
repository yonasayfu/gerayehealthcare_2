<?php

namespace App\Http\Controllers\Insurance;

use App\Http\Controllers\Controller;
use App\Http\Traits\ExportableTrait;
use App\Http\Config\AdditionalExportConfigs;
use App\Models\InsuranceClaim;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Mail;
use App\Mail\InsuranceClaimEmail;
use Inertia\Inertia;
use App\Http\Requests\StoreInsuranceClaimRequest;
use App\Http\Requests\UpdateInsuranceClaimRequest;
use App\Http\Requests\SendClaimEmailRequest;

class InsuranceClaimController extends Controller
{
    use ExportableTrait;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = InsuranceClaim::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('claim_status', 'ilike', "%{$search}%")
                  ->orWhere('receipt_number', 'ilike', "%{$search}%");
        }

        if ($request->filled('sort') && !empty($request->input('sort'))) {
            $query->orderBy($request->input('sort'), $request->input('direction', 'asc'));
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $insuranceClaims = $query->paginate($request->input('per_page', 5))->withQueryString();

        return Inertia::render('Insurance/Claims/Index', [
            'insuranceClaims' => $insuranceClaims,
            'filters' => $request->only(['search', 'sort', 'direction', 'per_page']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Insurance/Claims/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreInsuranceClaimRequest $request)
    {
        $validated = $request->validated();

        InsuranceClaim::create($validated);

        return Redirect::route('admin.insurance-claims.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $insuranceClaim = InsuranceClaim::with(['patient', 'invoice'])->findOrFail($id);
        return Inertia::render('Insurance/Claims/Show', [
            'insuranceClaim' => $insuranceClaim,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $insuranceClaim = InsuranceClaim::findOrFail($id);
        return Inertia::render('Insurance/Claims/Edit', [
            'insuranceClaim' => $insuranceClaim,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateInsuranceClaimRequest $request, string $id)
    {
        $insuranceClaim = InsuranceClaim::findOrFail($id);

        $validated = $request->validated();

        $insuranceClaim->update($validated);

        return Redirect::route('admin.insurance-claims.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $insuranceClaim = InsuranceClaim::findOrFail($id);

        $insuranceClaim->delete();

        return Redirect::route('admin.insurance-claims.index');
    }

    public function export(Request $request)
    {
        return $this->handleExport($request, InsuranceClaim::class, AdditionalExportConfigs::getInsuranceClaimConfig());
    }

    public function printSingle(InsuranceClaim $insuranceClaim)
    {
        return $this->handlePrintSingle($insuranceClaim, AdditionalExportConfigs::getInsuranceClaimConfig());
    }

    public function printCurrent(Request $request)
    {
        return $this->handlePrintCurrent($request, InsuranceClaim::class, AdditionalExportConfigs::getInsuranceClaimConfig());
    }

    public function printAll(Request $request)
    {
        return $this->handlePrintAll($request, InsuranceClaim::class, AdditionalExportConfigs::getInsuranceClaimConfig());
    }

    public function sendClaimEmail(SendClaimEmailRequest $request, string $id)
    {
        $insuranceClaim = InsuranceClaim::with(['invoice.patient', 'invoice.visitService'])->findOrFail($id);

        $validated = $request->validated();

        try {
            // Generate PDF
            $pdf = Pdf::loadView('pdf.insurance_claim_single', ['insuranceClaim' => $insuranceClaim])->setPaper('a4', 'portrait');
            $pdfPath = storage_path('app/public/temp_claim_' . $insuranceClaim->id . '.pdf');
            $pdf->save($pdfPath);

            // Send email
            Mail::to($request->input('recipient_email'))
                ->send(new InsuranceClaimEmail($insuranceClaim, $pdfPath));

            // Update claim status
            $insuranceClaim->update([
                'email_sent_at' => now(),
                'email_status' => 'Sent',
            ]);

            // Clean up temporary PDF file
            unlink($pdfPath);

            return Redirect::back()->with('success', 'Insurance claim email sent successfully!');
        } catch (\Exception $e) {
            // Log the error for debugging
            logger()->error('Failed to send insurance claim email: ' . $e->getMessage());

            // Update claim status to failed
            $insuranceClaim->update([
                'email_status' => 'Failed',
            ]);

            return Redirect::back()->with('error', 'Failed to send insurance claim email. Please try again.');
        }
    }
}
