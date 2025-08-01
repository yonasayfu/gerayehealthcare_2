<!DOCTYPE html>
<html>
<head>
    <title>Insurance Company - {{ $insuranceCompany->name }}</title>
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
        <p>Insurance Company Details</p>
    </header>

    <div class="detail-section">
        <h2>Company Information</h2>
        <div class="detail-row"><span class="detail-label">Name:</span> <span class="detail-value">{{ $insuranceCompany->name }}</span></div>
        <div class="detail-row"><span class="detail-label">Name (Amharic):</span> <span class="detail-value">{{ $insuranceCompany->name_amharic }}</span></div>
        <div class="detail-row"><span class="detail-label">Contact Person:</span> <span class="detail-value">{{ $insuranceCompany->contact_person }}</span></div>
        <div class="detail-row"><span class="detail-label">Contact Email:</span> <span class="detail-value">{{ $insuranceCompany->contact_email }}</span></div>
        <div class="detail-row"><span class="detail-label">Contact Phone:</span> <span class="detail-value">{{ $insuranceCompany->contact_phone }}</span></div>
        <div class="detail-row"><span class="detail-label">Address:</span> <span class="detail-value">{{ $insuranceCompany->address }}</span></div>
        <div class="detail-row"><span class="detail-label">Address (Amharic):</span> <span class="detail-value">{{ $insuranceCompany->address_amharic }}</span></div>
    </div>

    <div class="footer">
        Generated on {{ now()->format('F j, Y, g:i a') }}
    </div>
</body>
</html>
