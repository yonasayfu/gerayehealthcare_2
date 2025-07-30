<!DOCTYPE html>
<html>
<head>
    <title>Inventory Item Details</title>
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
        <h1>Geraye Home Care Services</h1>
        <p>Inventory Item Details Report</p>
    </div>

    <div class="details-section">
        <h2>Basic Information</h2>
        <div class="detail-row">
            <span class="detail-label">Name:</span>
            <span class="detail-value">{{ $inventoryItem->name }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Category:</span>
            <span class="detail-value">{{ $inventoryItem->item_category ?? '-' }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Type:</span>
            <span class="detail-value">{{ $inventoryItem->item_type ?? '-' }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Serial Number:</span>
            <span class="detail-value">{{ $inventoryItem->serial_number ?? '-' }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Status:</span>
            <span class="detail-value">{{ $inventoryItem->status ?? '-' }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Description:</span>
            <span class="detail-value">{{ $inventoryItem->description ?? '-' }}</span>
        </div>
    </div>

    <div class="details-section">
        <h2>Acquisition & Assignment</h2>
        <div class="detail-row">
            <span class="detail-label">Purchase Date:</span>
            <span class="detail-value">{{ $inventoryItem->purchase_date ? \Carbon\Carbon::parse($inventoryItem->purchase_date)->format('Y-m-d') : '-' }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Warranty Expiry:</span>
            <span class="detail-value">{{ $inventoryItem->warranty_expiry ? \Carbon\Carbon::parse($inventoryItem->warranty_expiry)->format('Y-m-d') : '-' }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Supplier:</span>
            <span class="detail-value">{{ $inventoryItem->supplier->name ?? '-' }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Assigned To Type:</span>
            <span class="detail-value">{{ $inventoryItem->assigned_to_type ?? '-' }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Assigned To ID:</span>
            <span class="detail-value">{{ $inventoryItem->assigned_to_id ?? '-' }}</span>
        </div>
    </div>

    <div class="details-section">
        <h2>Maintenance Details</h2>
        <div class="detail-row">
            <span class="detail-label">Last Maintenance Date:</span>
            <span class="detail-value">{{ $inventoryItem->last_maintenance_date ? \Carbon\Carbon::parse($inventoryItem->last_maintenance_date)->format('Y-m-d') : '-' }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Next Maintenance Due:</span>
            <span class="detail-value">{{ $inventoryItem->next_maintenance_due ? \Carbon\Carbon::parse($inventoryItem->next_maintenance_due)->format('Y-m-d') : '-' }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Maintenance Schedule:</span>
            <span class="detail-value">{{ $inventoryItem->maintenance_schedule ?? '-' }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Notes:</span>
            <span class="detail-value">{{ $inventoryItem->notes ?? '-' }}</span>
        </div>
    </div>

    <div class="footer">
        Generated on: {{ now()->format('F j, Y, g:i a') }}
    </div>
</body>
</html>
