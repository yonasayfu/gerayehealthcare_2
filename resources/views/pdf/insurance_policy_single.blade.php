<!DOCTYPE html>
<html>
<head>
    <title>Insurance Policy - {{ $insurancePolicy->service_type }}</title>
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
        <p>Insurance Policy Details</p>
    </header>

    <div class="detail-section">
        <h2>Policy Information</h2>
        <div class="detail-row"><span class="detail-label">Service Type:</span> <span class="detail-value">{{ $insurancePolicy->service_type }}</span></div>
        <div class="detail-row"><span class="detail-label">Service Type (Amharic):</span> <span class="detail-value">{{ $insurancePolicy->service_type_amharic }}</span></div>
        <div class="detail-row"><span class="detail-label">Coverage Percentage:</span> <span class="detail-value">{{ $insurancePolicy->coverage_percentage }}%</span></div>
        <div class="detail-row"><span class="detail-label">Coverage Type:</span> <span class="detail-value">{{ $insurancePolicy->coverage_type }}</span></div>
        <div class="detail-row"><span class="detail-label">Is Active:</span> <span class="detail-value">{{ $insurancePolicy->is_active ? 'Yes' : 'No' }}</span></div>
        <div class="detail-row"><span class="detail-label">Notes:</span> <span class="detail-value">{{ $insurancePolicy->notes }}</span></div>
        <div class="detail-row"><span class="detail-label">Created At:</span> <span class="detail-value">{{ $insurancePolicy->created_at }}</span></div>
        <div class="detail-row"><span class="detail-label">Updated At:</span> <span class="detail-value">{{ $insurancePolicy->updated_at }}</span></div>
    </div>

    <div class="footer">
        Generated on {{ now()->format('F j, Y, g:i a') }}
    </div>
</body>
</html>
