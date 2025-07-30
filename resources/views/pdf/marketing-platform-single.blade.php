<!DOCTYPE html>
<html>
<head>
    <title>Marketing Platform Details</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10px;
            margin: 0.5cm;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header img {
            max-width: 100px;
            margin-bottom: 10px;
        }
        .header h1 {
            font-size: 18px;
            margin: 0;
        }
        .header p {
            font-size: 12px;
            margin: 0;
        }
        .details-section {
            margin-bottom: 15px;
        }
        .details-section h2 {
            font-size: 14px;
            margin-bottom: 10px;
            border-bottom: 1px solid #eee;
            padding-bottom: 5px;
        }
        .detail-item {
            margin-bottom: 5px;
        }
        .detail-item label {
            font-weight: bold;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 9px;
            position: fixed;
            bottom: 0.5cm;
            width: 100%;
            left: 0;
            right: 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="data:image/jpeg;base64,{{ base64_encode(file_get_contents(public_path('images/geraye_logo.jpeg'))) }}" alt="Geraye Logo">
        <h1>Geraye Home Care Services</h1>
        <p>Marketing Platform Details</p>
        <hr>
    </div>

    <div class="details-section">
        <h2>Platform Information</h2>
        <div class="detail-item">
            <label>Platform Name:</label> {{ $platform->name }}
        </div>
        <div class="detail-item">
            <label>API Endpoint:</label> {{ $platform->api_endpoint ?? '-' }}
        </div>
        <div class="detail-item">
            <label>API Credentials:</label> {{ $platform->api_credentials ?? '-' }}
        </div>
        <div class="detail-item">
            <label>Is Active:</label> {{ $platform->is_active ? 'Yes' : 'No' }}
        </div>
    </div>

    <div class="details-section">
        <h2>Timestamps</h2>
        <div class="detail-item">
            <label>Created At:</label> {{ $platform->created_at ? \Carbon\Carbon::parse($platform->created_at)->format('M d, Y H:i:s') : '-' }}
        </div>
        <div class="detail-item">
            <label>Updated At:</label> {{ $platform->updated_at ? \Carbon\Carbon::parse($platform->updated_at)->format('M d, Y H:i:s') : '-' }}
        </div>
    </div>

    <div class="footer">
        <hr>
        <p>Document Generated: {{ \Carbon\Carbon::now()->format('M d, Y H:i:s') }}</p>
    </div>
</body>
</html>
