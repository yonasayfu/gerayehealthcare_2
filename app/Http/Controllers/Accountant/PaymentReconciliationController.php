<?php

namespace App\Http\Controllers\Accountant;

use App\Http\Controllers\Controller;
use App\Services\Insurance\InsuranceClaimService;
use App\Services\InvoiceService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PaymentReconciliationController extends Controller
{
    protected $insuranceClaimService;

    protected $invoiceService;

    public function __construct(InsuranceClaimService $insuranceClaimService, InvoiceService $invoiceService)
    {
        $this->insuranceClaimService = $insuranceClaimService;
        $this->invoiceService = $invoiceService;
    }

    public function index(Request $request)
    {
        $pendingClaims = $this->insuranceClaimService->getPendingClaims();
        // You might also want to fetch pending invoices that are not linked to claims
        // For simplicity, we'll focus on claims for now.

        return Inertia::render('Accountant/Reconciliation/Index', [
            'pendingClaims' => $pendingClaims,
        ]);
    }

    public function processClaimPayment(Request $request, int $claimId)
    {
        $request->validate([
            'paid_amount' => 'required|numeric|min:0',
            'payment_received_at' => 'nullable|date',
            'payment_method' => 'nullable|string|max:255',
            'receipt_number' => 'nullable|string|max:255',
        ]);

        try {
            $claim = $this->insuranceClaimService->processPayment($claimId, $request->all());

            return back()->with('banner', 'Claim payment processed successfully.')->with('bannerStyle', 'success');
        } catch (\Exception $e) {
            return back()->with('banner', 'Failed to process claim payment: '.$e->getMessage())->with('bannerStyle', 'danger');
        }
    }

    // You might add similar methods for processing invoice payments directly if needed
}
