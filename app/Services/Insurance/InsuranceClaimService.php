<?php

namespace App\Services\Insurance;

use App\Models\InsuranceClaim;
use App\Services\Base\BaseService;
use Illuminate\Support\Facades\Log;

class InsuranceClaimService extends BaseService
{
    public function __construct(InsuranceClaim $insuranceClaim)
    {
        parent::__construct($insuranceClaim);
    }

    public function create(array|object $data): InsuranceClaim
    {
        $data = is_object($data) ? (array) $data : $data;

        return parent::create($data);
    }

    /**
     * Process payment for an insurance claim
     */
    public function processPayment(int $claimId, array $paymentData): InsuranceClaim
    {
        $claim = $this->getById($claimId);

        $updateData = [
            'paid_amount' => $paymentData['paid_amount'],
            'payment_received_at' => $paymentData['payment_received_at'] ?? now(),
            'payment_method' => $paymentData['payment_method'] ?? 'Bank Transfer',
            'claim_status' => 'Paid',
            'processed_at' => now(),
        ];

        if (isset($paymentData['receipt_number'])) {
            $updateData['receipt_number'] = $paymentData['receipt_number'];
        }

        $updatedClaim = $this->update($claimId, $updateData);

        // Update related invoice status if fully paid
        if ($updatedClaim->invoice && $updatedClaim->paid_amount >= $updatedClaim->coverage_amount) {
            $updatedClaim->invoice->update(['status' => 'Paid', 'paid_at' => now()]);
        }

        Log::info("Payment processed for insurance claim {$claimId}: {$paymentData['paid_amount']} ETB");

        return $updatedClaim;
    }

    /**
     * Update claim status (e.g., from Submitted to Approved/Denied)
     */
    public function updateClaimStatus(int $claimId, string $status, ?string $denialReason = null): InsuranceClaim
    {
        $updateData = [
            'claim_status' => $status,
            'processed_at' => now(),
        ];

        if ($status === 'Denied' && $denialReason) {
            $updateData['denial_reason'] = $denialReason;
        }

        return $this->update($claimId, $updateData);
    }

    /**
     * Get claims by status for reporting
     */
    public function getClaimsByStatus(string $status)
    {
        return $this->model->where('claim_status', $status)
            ->with(['patient', 'invoice', 'insuranceCompany', 'policy'])
            ->get();
    }

    /**
     * Get pending claims for follow-up
     */
    public function getPendingClaims()
    {
        return $this->model->whereIn('claim_status', ['Submitted', 'Processing'])
            ->with(['patient', 'invoice', 'insuranceCompany', 'policy'])
            ->orderBy('submitted_at')
            ->get();
    }
}
