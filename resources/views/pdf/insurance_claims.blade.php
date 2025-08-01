<!DOCTYPE html>
<html>
<head>
    <title>Insurance Claims List - Geraye Home Care Services</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; font-size: 12px; color: #333; }
        header { text-align: center; margin-bottom: 30px; }
        h1 { font-size: 20px; margin: 0; }
        p { font-size: 14px; margin: 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { padding: 8px 10px; border: 1px solid #999; text-align: left; }
        th { background-color: #f3f3f3; }
        .footer { text-align: right; margin-top: 30px; font-size: 11px; }
    </style>
</head>
<body>
    <header>
        <img src="{{ public_path('images/geraye_logo.jpeg') }}" alt="Geraye Logo" style="max-height: 60px; margin-bottom: 10px;">
        <h1>Geraye Home Care Services</h1>
        <p>Insurance Claims List</p>
    </header>

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

    <div class="footer">
        Generated on {{ now()->format('F j, Y, g:i a') }}
    </div>
</body>
</html>
