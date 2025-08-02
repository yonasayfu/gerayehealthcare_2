<x-printable-report
    title="Insurance Claims List - Geraye Home Care Services"
    :data="$insuranceClaims->map(function($claim) {
        return [
            'claim_status' => $claim->claim_status,
            'coverage_amount' => $claim->coverage_amount,
            'paid_amount' => $claim->paid_amount,
            'submitted_at' => $claim->submitted_at,
            'processed_at' => $claim->processed_at,
            'payment_due_date' => $claim->payment_due_date,
            'payment_received_at' => $claim->payment_received_at,
            'payment_method' => $claim->payment_method,
            'reimbursement_required' => $claim->reimbursement_required ? 'Yes' : 'No',
            'receipt_number' => $claim->receipt_number,
            'is_pre_authorized' => $claim->is_pre_authorized ? 'Yes' : 'No',
            'pre_authorization_code' => $claim->pre_authorization_code,
            'denial_reason' => $claim->denial_reason,
        ];
    })->toArray()"
    :columns="[
        ['key' => 'claim_status', 'label' => 'Claim Status'],
        ['key' => 'coverage_amount', 'label' => 'Coverage Amount'],
        ['key' => 'paid_amount', 'label' => 'Paid Amount'],
        ['key' => 'submitted_at', 'label' => 'Submitted At'],
        ['key' => 'processed_at', 'label' => 'Processed At'],
        ['key' => 'payment_due_date', 'label' => 'Payment Due Date'],
        ['key' => 'payment_received_at', 'label' => 'Payment Received At'],
        ['key' => 'payment_method', 'label' => 'Payment Method'],
        ['key' => 'reimbursement_required', 'label' => 'Reimbursement Required'],
        ['key' => 'receipt_number', 'label' => 'Receipt Number'],
        ['key' => 'is_pre_authorized', 'label' => 'Is Pre-Authorized'],
        ['key' => 'pre_authorization_code', 'label' => 'Pre-Authorization Code'],
        ['key' => 'denial_reason', 'label' => 'Denial Reason'],
    ]"
    :header-info="[
        'logoSrc' => public_path('images/geraye_logo.jpeg'),
        'clinicName' => 'Geraye Home Care Services',
        'documentTitle' => 'Insurance Claims List',
    ]"
    :footer-info="[
        'generatedDate' => true,
    ]"
/>