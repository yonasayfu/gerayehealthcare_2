<?php

namespace App\Http\Controllers\Insurance;

use App\Http\Controllers\Controller;
use App\Models\InsuranceClaim;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Mail;
use App\Mail\InsuranceClaimEmail;
use Inertia\Inertia;

class InsuranceClaimController extends Controller
{
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
    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'invoice_id' => 'nullable|exists:invoices,id',
            'insurance_company_id' => 'nullable|exists:insurance_companies,id',
            'policy_id' => 'nullable|exists:insurance_policies,id',
            'claim_status' => 'required|string|max:255',
            'coverage_amount' => 'nullable|numeric',
            'paid_amount' => 'nullable|numeric',
            'submitted_at' => 'nullable|date',
            'processed_at' => 'nullable|date',
            'payment_due_date' => 'nullable|date',
            'payment_received_at' => 'nullable|date',
            'payment_method' => 'nullable|string|max:255',
            'reimbursement_required' => 'required|boolean',
            'receipt_number' => 'nullable|string|max:255',
            'is_pre_authorized' => 'required|boolean',
            'pre_authorization_code' => 'nullable|string|max:255',
            'denial_reason' => 'nullable|string',
            'translated_notes' => 'nullable|string',
        ]);

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
    public function update(Request $request, string $id)
    {
        $insuranceClaim = InsuranceClaim::findOrFail($id);

        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'invoice_id' => 'nullable|exists:invoices,id',
            'insurance_company_id' => 'nullable|exists:insurance_companies,id',
            'policy_id' => 'nullable|exists:insurance_policies,id',
            'claim_status' => 'required|string|max:255',
            'coverage_amount' => 'nullable|numeric',
            'paid_amount' => 'nullable|numeric',
            'submitted_at' => 'nullable|date',
            'processed_at' => 'nullable|date',
            'payment_due_date' => 'nullable|date',
            'payment_received_at' => 'nullable|date',
            'payment_method' => 'nullable|string|max:255',
            'reimbursement_required' => 'required|boolean',
            'receipt_number' => 'nullable|string|max:255',
            'is_pre_authorized' => 'required|boolean',
            'pre_authorization_code' => 'nullable|string|max:255',
            'denial_reason' => 'nullable|string',
            'translated_notes' => 'nullable|string',
        ]);

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
        $type = $request->get('type');
        $insuranceClaims = InsuranceClaim::select('claim_status', 'coverage_amount', 'paid_amount', 'submitted_at', 'processed_at', 'payment_due_date', 'payment_received_at', 'payment_method', 'reimbursement_required', 'receipt_number', 'is_pre_authorized', 'pre_authorization_code', 'denial_reason')->get();

        if ($type === 'csv') {
            $csvData = "Claim Status,Coverage Amount,Paid Amount,Submitted At,Processed At,Payment Due Date,Payment Received At,Payment Method,Reimbursement Required,Receipt Number,Is Pre-Authorized,Pre-Authorization Code,Denial Reason\n";
            foreach ($insuranceClaims as $claim) {
                $csvData .= "\"{$claim->claim_status}\",\"{$claim->coverage_amount}\",\"{$claim->paid_amount}\",\"{$claim->submitted_at}\",\"{$claim->processed_at}\",\"{$claim->payment_due_date}\",\"{$claim->payment_received_at}\",\"{$claim->payment_method}\",\"{$claim->reimbursement_required}\",\"{$claim->receipt_number}\",\"{$claim->is_pre_authorized}\",\"{$claim->pre_authorization_code}\",\"{$claim->denial_reason}\"\n";
            }

            return Response::make($csvData, 200, [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="insurance_claims.csv"',
            ]);
        }

        if ($type === 'pdf') {
            $pdf = Pdf::loadView('pdf.insurance_claims', ['insuranceClaims' => $insuranceClaims])->setPaper('a4', 'landscape');
            return $pdf->stream('insurance_claims.pdf');
        }

        return abort(400, 'Invalid export type');
    }

    public function printSingle(InsuranceClaim $insuranceClaim)
    {
        $pdf = Pdf::loadView('pdf.insurance_claim_single', ['insuranceClaim' => $insuranceClaim])->setPaper('a4', 'portrait');
        return $pdf->stream("insurance_claim-{$insuranceClaim->id}.pdf");
    }

    public function printCurrent(Request $request)
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

        $insuranceClaims = $query->paginate($request->input('per_page', 5))->appends($request->except('page'));

        return Inertia::render('Insurance/Claims/PrintCurrent', ['insuranceClaims' => $insuranceClaims->items()]);
    }

    public function printAll(Request $request)
    {
        $insuranceClaims = InsuranceClaim::orderBy('claim_status')->get();

        $pdf = Pdf::loadView('pdf.insurance_claims', ['insuranceClaims' => $insuranceClaims])->setPaper('a4', 'landscape');
        return $pdf->stream('insurance_claims.pdf');
    }

    public function sendClaimEmail(Request $request, string $id)
    {
        $insuranceClaim = InsuranceClaim::with(['invoice.patient', 'invoice.visitService'])->findOrFail($id);

        $request->validate([
            'recipient_email' => 'required|email',
            'subject' => 'required|string',
            'message' => 'nullable|string',
        ]);

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
