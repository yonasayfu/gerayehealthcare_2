<!DOCTYPE html>
<html>
<head>
    <title>Exchange Rate - {{ $exchangeRate->currency_code }}</title>
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
        <p>Exchange Rate Details</p>
    </header>

    <div class="detail-section">
        <h2>Rate Information</h2>
        <div class="detail-row"><span class="detail-label">Currency Code:</span> <span class="detail-value">{{ $exchangeRate->currency_code }}</span></div>
        <div class="detail-row"><span class="detail-label">Rate to ETB:</span> <span class="detail-value">{{ $exchangeRate->rate_to_etb }}</span></div>
        <div class="detail-row"><span class="detail-label">Source:</span> <span class="detail-value">{{ $exchangeRate->source }}</span></div>
        <div class="detail-row"><span class="detail-label">Date Effective:</span> <span class="detail-value">{{ $exchangeRate->date_effective }}</span></div>
    </div>

    <div class="footer">
        Generated on {{ now()->format('F j, Y, g:i a') }}
    </div>
</body>
</html>
