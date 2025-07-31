<!DOCTYPE html>
<html>
<head>
    <title>Insurance Claim Details</title>
    <style>
        body { font-family: sans-serif; }
        .detail-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .detail-table th, .detail-table td { border: 1px solid black; padding: 8px; text-align: left; }
        .detail-table th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h1>Insurance Claim Details</h1>

    <table class="detail-table">
        <tr>
            <th>ID</th>
            <td>{{ $insuranceClaim->id }}</td>
        </tr>
        <tr>
            <th>Patient ID</th>
            <td>{{ $insuranceClaim->patient_id }}</td>
        </tr>
        <tr>
            <th>Invoice ID</th>
            <td>{{ $insuranceClaim->invoice_id }}</td>
        </tr>
        <tr>
            <th>Insurance Company ID</th>
            <td>{{ $insuranceClaim->insurance_company_id }}</td>
        </tr>
        <tr>
            <th>Policy ID</th>
            <td>{{ $insuranceClaim->policy_id }}</td>
        </tr>
        <tr>
            <th>Claim Status</th>
            <td>{{ $insuranceClaim->claim_status }}</td>
        </tr>
        <tr>
            <th>Coverage Amount</th>
            <td>{{ $insuranceClaim->coverage_amount }}</td>
        </tr>
        <tr>
            <th>Paid Amount</th>
            <td>{{ $insuranceClaim->paid_amount }}</td>
        </tr>
        <tr>
            <th>Submitted At</th>
            <td>{{ $insuranceClaim->submitted_at }}</td>
        </tr>
        <tr>
            <th>Processed At</th>
            <td>{{ $insuranceClaim->processed_at }}</td>
        </tr>
        <tr>
            <th>Payment Due Date</th>
            <td>{{ $insuranceClaim->payment_due_date }}</td>
        </tr>
        <tr>
            <th>Payment Received At</th>
            <td>{{ $insuranceClaim->payment_received_at }}</td>
        </tr>
        <tr>
            <th>Payment Method</th>
            <td>{{ $insuranceClaim->payment_method }}</td>
        </tr>
        <tr>
            <th>Reimbursement Required</th>
            <td>{{ $insuranceClaim->reimbursement_required ? 'Yes' : 'No' }}</td>
        </tr>
        <tr>
            <th>Receipt Number</th>
            <td>{{ $insuranceClaim->receipt_number }}</td>
        </tr>
        <tr>
            <th>Is Pre-Authorized</th>
            <td>{{ $insuranceClaim->is_pre_authorized ? 'Yes' : 'No' }}</td>
        </tr>
        <tr>
            <th>Pre-Authorization Code</th>
            <td>{{ $insuranceClaim->pre_authorization_code }}</td>
        </tr>
        <tr>
            <th>Denial Reason</th>
            <td>{{ $insuranceClaim->denial_reason }}</td>
        </tr>
        <tr>
            <th>Translated Notes</th>
            <td>{{ $insuranceClaim->translated_notes }}</td>
        </tr>
        <tr>
            <th>Created At</th>
            <td>{{ $insuranceClaim->created_at }}</td>
        </tr>
        <tr>
            <th>Updated At</th>
            <td>{{ $insuranceClaim->updated_at }}</td>
        </tr>
    </table>
</body>
</html>
