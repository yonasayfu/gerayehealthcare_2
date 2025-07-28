<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Marketing Campaign Details - {{ $campaign->campaign_name }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            font-size: 12px;
            color: #333;
        }
        header {
            text-align: center;
            margin-bottom: 30px;
        }
        h1 {
            font-size: 20px;
            margin: 0;
        }
        p {
            font-size: 14px;
            margin: 0;
        }
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        .details-table th, .details-table td {
            padding: 8px 10px;
            border: 1px solid #eee;
            text-align: left;
        }
        .details-table th {
            background-color: #f9f9f9;
            width: 30%;
        }
        .footer {
            text-align: right;
            margin-top: 30px;
            font-size: 11px;
        }
        pre {
            white-space: pre-wrap;
            word-wrap: break-word;
            background-color: #f0f0f0;
            padding: 10px;
            border-radius: 5px;
        }
    </style>
</head>
<body>
   <header style="text-align: center; margin-bottom: 30px;">
    <img src="{{ public_path('images/geraye_logo.jpeg') }}" alt="Geraye Logo" style="max-height: 60px; margin-bottom: 10px;">
    <h1 style="margin: 0;">Geraye Home-to-Home Care</h1>
    <p style="margin: 0;">Marketing Campaign Details</p>
</header>

    <table class="details-table">
        <tr>
            <th>Campaign Name</th>
            <td>{{ $campaign->campaign_name }}</td>
        </tr>
        <tr>
            <th>Campaign Code</th>
            <td>{{ $campaign->campaign_code ?? '-' }}</td>
        </tr>
        <tr>
            <th>Platform</th>
            <td>{{ $campaign->platform->name ?? '-' }}</td>
        </tr>
        <tr>
            <th>Campaign Type</th>
            <td>{{ $campaign->campaign_type ?? '-' }}</td>
        </tr>
        <tr>
            <th>Status</th>
            <td>{{ $campaign->status ?? '-' }}</td>
        </tr>
        <tr>
            <th>Start Date</th>
            <td>{{ $campaign->start_date ?? '-' }}</td>
        </tr>
        <tr>
            <th>End Date</th>
            <td>{{ $campaign->end_date ?? '-' }}</td>
        </tr>
        <tr>
            <th>Budget Allocated</th>
            <td>{{ number_format($campaign->budget_allocated ?? 0, 2) }}</td>
        </tr>
        <tr>
            <th>Budget Spent</th>
            <td>{{ number_format($campaign->budget_spent ?? 0, 2) }}</td>
        </tr>
        <tr>
            <th>UTM Campaign</th>
            <td>{{ $campaign->utm_campaign ?? '-' }}</td>
        </tr>
        <tr>
            <th>UTM Source</th>
            <td>{{ $campaign->utm_source ?? '-' }}</td>
        </tr>
        <tr>
            <th>UTM Medium</th>
            <td>{{ $campaign->utm_medium ?? '-' }}</td>
        </tr>
        <tr>
            <th>Assigned Staff</th>
            <td>{{ $campaign->assignedStaff->full_name ?? '-' }}</td>
        </tr>
        <tr>
            <th>Created By</th>
            <td>{{ $campaign->createdByStaff->full_name ?? '-' }}</td>
        </tr>
        <tr>
            <th>Created At</th>
            <td>{{ \Carbon\Carbon::parse($campaign->created_at)->format('F j, Y, g:i a') }}</td>
        </tr>
        <tr>
            <th>Updated At</th>
            <td>{{ \Carbon\Carbon::parse($campaign->updated_at)->format('F j, Y, g:i a') }}</td>
        </tr>
        <tr>
            <th>Target Audience</th>
            <td><pre>{{ json_encode($campaign->target_audience, JSON_PRETTY_PRINT) }}</pre></td>
        </tr>
        <tr>
            <th>Goals</th>
            <td><pre>{{ json_encode($campaign->goals, JSON_PRETTY_PRINT) }}</pre></td>
        </tr>
    </table>

    <div class="footer">
        Generated on {{ now()->format('F j, Y, g:i a') }}
    </div>
</body>
</html>
