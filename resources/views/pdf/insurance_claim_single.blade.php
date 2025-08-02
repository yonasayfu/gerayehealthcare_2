<x-printable-report
    title="Insurance Claim - {{ $insuranceClaim->id }}"
    :data="[
        ['label' => 'Claim ID', 'value' => $insuranceClaim->id],
        ['label' => 'Patient ID', 'value' => $insuranceClaim->patient_id],
        ['label' => 'Invoice ID', 'value' => $insuranceClaim->invoice_id],
        ['label' => 'Insurance Company ID', 'value' => $insuranceClaim->insurance_company_id],
        ['label' => 'Policy ID', 'value' => $insuranceClaim->policy_id],
        ['label' => 'Claim Status', 'value' => $insuranceClaim->claim_status],
        ['label' => 'Coverage Amount', 'value' => $insuranceClaim->coverage_amount],
        ['label' => 'Paid Amount', 'value' => $insuranceClaim->paid_amount],
        ['label' => 'Submitted At', 'value' => $insuranceClaim->submitted_at],
        ['label' => 'Processed At', 'value' => $insuranceClaim->processed_at],
        ['label' => 'Payment Due Date', 'value' => $insuranceClaim->payment_due_date],
        ['label' => 'Payment Received At', 'value' => $insuranceClaim->payment_received_at],
        ['label' => 'Payment Method', 'value' => $insuranceClaim->payment_method],
        ['label' => 'Reimbursement Required', 'value' => $insuranceClaim->reimbursement_required ? 'Yes' : 'No'],
        ['label' => 'Receipt Number', 'value' => $insuranceClaim->receipt_number],
        ['label' => 'Is Pre-Authorized', 'value' => $insuranceClaim->is_pre_authorized ? 'Yes' : 'No'],
        ['label' => 'Pre-Authorization Code', 'value' => $insuranceClaim->pre_authorization_code],
        ['label' => 'Denial Reason', 'value' => $insuranceClaim->denial_reason],
        ['label' => 'Translated Notes', 'value' => $insuranceClaim->translated_notes],
    ]"
    :columns="[
        ['key' => 'label', 'label' => 'Field', 'printWidth' => '30%'],
        ['key' => 'value', 'label' => 'Value', 'printWidth' => '70%'],
    ]"
    :header-info="[
        'logoSrc' => public_path('images/geraye_logo.jpeg'),
        'clinicName' => 'Geraye Home Care Services',
        'documentTitle' => 'Insurance Claim Details',
    ]"
    :footer-info="[
        'generatedDate' => true,
    ]"
/>