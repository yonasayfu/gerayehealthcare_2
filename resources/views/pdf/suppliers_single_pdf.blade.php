<!DOCTYPE html>
<html>
<head>
    <title>Supplier Details</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 20mm;
        }
        body {
            font-family: sans-serif;
            margin: 0;
            padding: 0;
            font-size: 10pt;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header img {
            max-height: 80px;
            margin-bottom: 10px;
        }
        .header h1 {
            font-size: 20pt;
            margin: 0;
            color: #333;
        }
        .header p {
            font-size: 10pt;
            color: #777;
        }
        .details-section {
            margin-bottom: 20px;
            border-bottom: 1px solid #eee;
            padding-bottom: 15px;
        }
        .details-section:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }
        .details-section h2 {
            font-size: 14pt;
            color: #555;
            margin-bottom: 10px;
        }
        .detail-row {
            display: flex;
            margin-bottom: 5px;
        }
        .detail-label {
            font-weight: bold;
            width: 150px;
            flex-shrink: 0;
        }
        .detail-value {
            flex-grow: 1;
        }
        .footer {
            text-align: center;
            margin-top: 50px;
            font-size: 8pt;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ public_path('images/geraye_logo.jpeg') }}" alt="Geraye Logo">
        <h1>Geraye Home-to-Home Care</h1>
        <p>Supplier Details Report</p>
    </div>

    <div class="details-section">
        <h2>Supplier Information</h2>
        <div class="detail-row">
            <span class="detail-label">Name:</span>
            <span class="detail-value">{{ $supplier->name }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Contact Person:</span>
            <span class="detail-value">{{ $supplier->contact_person ?? '-' }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Email:</span>
            <span class="detail-value">{{ $supplier->email ?? '-' }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Phone:</span>
            <span class="detail-value">{{ $supplier->phone ?? '-' }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Address:</span>
            <span class="detail-value">{{ $supplier->address ?? '-' }}</span>
        </div>
    </div>

    <div class="footer">
        Generated on: {{ now()->format('F j, Y, g:i a') }}
    </div>
</body>
</html>
