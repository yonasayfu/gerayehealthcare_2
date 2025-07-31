<!DOCTYPE html>
<html>
<head>
    <title>Insurance Claims</title>
    <style>
        body { font-family: sans-serif; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid black; padding: 8px; text-align: left; }
    </style>
</head>
<body>
    <h1>Insurance Claims</h1>
    <table>
        <thead>
            <tr>
                <th>Claim Status</th>
                <th>Coverage Amount</th>
                <th>Paid Amount</th>
                <th>Submitted At</th>
                <th>Processed At</th>
                <th>Payment Due Date</th>
                <th>Payment Received At</th>
                <th>Payment Method</th>
                <th>Reimbursement Required</th>
                <th>Receipt Number</th>
                <th>Is Pre-Authorized</th>
                <th>Pre-Authorization Code</th>
                <th>Denial Reason</th>
            </tr>
        </thead>
        <tbody>
            @foreach($insuranceClaims as $claim)
            <tr>
                <td>{{ $claim->claim_status }}</td>
                <td>{{ $claim->coverage_amount }}</td>
                <td>{{ $claim->paid_amount }}</td>
                <td>{{ $claim->submitted_at }}</td>
                <td>{{ $claim->processed_at }}</td>
                <td>{{ $claim->payment_due_date }}</td>
                <td>{{ $claim->payment_received_at }}</td>
                <td>{{ $claim->payment_method }}</td>
                <td>{{ $claim->reimbursement_required ? 'Yes' : 'No' }}</td>
                <td>{{ $claim->receipt_number }}</td>
                <td>{{ $claim->is_pre_authorized ? 'Yes' : 'No' }}</td>
                <td>{{ $claim->pre_authorization_code }}</td>
                <td>{{ $claim->denial_reason }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
