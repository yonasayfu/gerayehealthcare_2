<!DOCTYPE html>
<html>
<head>
    <title>Insurance Claim - {{ $insuranceClaim->id }}</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; font-size: 12px; color: #333; }
        header { text-align: center; margin-bottom: 30px; }
        h1 { font-size: 20px; margin: 0; }
        p { font-size: 14px; margin: 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { padding: 8px 10px; border: 1px solid #999; text-align: left; }
        th { background-color: #f3f3f3; }
        .footer { text-align: right; margin-top: 30px; font-size: 11px; }
        .detail-section { margin-bottom: 20px; border-bottom: 1px solid #eee; padding-bottom: 10px; }
        .detail-section:last-child { border-bottom: none; }
        .detail-row { display: flex; margin-bottom: 5px; }
        .detail-label { font-weight: bold; width: 150px; /* Adjust as needed */ }
        .detail-value { flex-grow: 1; }
    </style>
</head>
<body>
    <header>
        <img src="{{ public_path('images/geraye_logo.jpeg') }}" alt="Geraye Logo" style="max-height: 60px; margin-bottom: 10px;">
        <h1>Geraye Home Care Services</h1>
        <p>Insurance Claim Details</p>
    </header>

    <div class="detail-section">
        <h2>Claim Information</h2>
        <div class="detail-row"><span class="detail-label">Claim ID:</span> <span class="detail-value">{{ $insuranceClaim->id }}</span></div>
        <div class="detail-row"><span class="detail-label">Patient ID:</span> <span class="detail-value">{{ $insuranceClaim->patient_id }}</span></div>
        <div class="detail-row"><span class="detail-label">Invoice ID:</span> <span class="detail-value">{{ $insuranceClaim->invoice_id }}</span></div>
        <div class="detail-row"><span class="detail-label">Insurance Company ID:</span> <span class="detail-value">{{ $insuranceClaim->insurance_company_id }}</span></div>
        <div class="detail-row"><span class="detail-label">Policy ID:</span> <span class="detail-value">{{ $insuranceClaim->policy_id }}</span></div>
        <div class="detail-row"><span class="detail-label">Claim Status:</span> <span class="detail-value">{{ $insuranceClaim->claim_status }}</span></div>
        <div class="detail-row"><span class="detail-label">Coverage Amount:</span> <span class="detail-value">{{ $insuranceClaim->coverage_amount }}</span></div>
        <div class="detail-row"><span class="detail-label">Paid Amount:</span> <span class="detail-value">{{ $insuranceClaim->paid_amount }}</span></div>
        <div class="detail-row"><span class="detail-label">Submitted At:</span> <span class="detail-value">{{ $insuranceClaim->submitted_at }}</span></div>
        <div class="detail-row"><span class="detail-label">Processed At:</span> <span class="detail-value">{{ $insuranceClaim->processed_at }}</span></div>
        <div class="detail-row"><span class="detail-label">Payment Due Date:</span> <span class="detail-value">{{ $insuranceClaim->payment_due_date }}</span></div>
        <div class="detail-row"><span class="detail-label">Payment Received At:</span> <span class="detail-value">{{ $insuranceClaim->payment_received_at }}</span></div>
        <div class="detail-row"><span class="detail-label">Payment Method:</span> <span class="detail-value">{{ $insuranceClaim->payment_method }}</span></div>
        <div class="detail-row"><span class="detail-label">Reimbursement Required:</span> <span class="detail-value">{{ $insuranceClaim->reimbursement_required ? 'Yes' : 'No' }}</span></div>
        <div class="detail-row"><span class="detail-label">Receipt Number:</span> <span class="detail-value">{{ $insuranceClaim->receipt_number }}</span></div>
        <div class="detail-row"><span class="detail-label">Is Pre-Authorized:</span> <span class="detail-value">{{ $insuranceClaim->is_pre_authorized ? 'Yes' : 'No' }}</span></div>
        <div class="detail-row"><span class="detail-label">Pre-Authorization Code:</span> <span class="detail-value">{{ $insuranceClaim->pre_authorization_code }}</span></div>
        <div class="detail-row"><span class="detail-label">Denial Reason:</span> <span class="detail-value">{{ $insuranceClaim->denial_reason }}</span></div>
        <div class="detail-row"><span class="detail-label">Translated Notes:</span> <span class="detail-value">{{ $insuranceClaim->translated_notes }}</span></div>
    </div>

    <div class="footer">
        Generated on {{ now()->format('F j, Y, g:i a') }}
    </div>
</body>
</html>
