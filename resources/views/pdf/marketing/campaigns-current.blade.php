<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Marketing Campaigns List (Current View) - Geraye</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            font-size: 10px; /* Smaller font for more columns */
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
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            padding: 5px 7px; /* Smaller padding */
            border: 1px solid #999;
            text-align: left;
            word-wrap: break-word; /* Allow text to wrap */
        }
        th {
            background-color: #f3f3f3;
        }
        .footer {
            text-align: right;
            margin-top: 30px;
            font-size: 11px;
        }
    </style>
</head>
<body>
   <header style="text-align: center; margin-bottom: 30px;">
    <img src="{{ public_path('images/geraye_logo.jpeg') }}" alt="Geraye Logo" style="max-height: 60px; margin-bottom: 10px;">
    <h1 style="margin: 0;">Geraye Home-to-Home Care</h1>
    <p style="margin: 0;">Marketing Campaigns List (Current View)</p>
</header>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Campaign Name</th>
                <th>Code</th>
                <th>Platform</th>
                <th>Type</th>
                <th>Status</th>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Budget Allocated</th>
                <th>Budget Spent</th>
                <th>Assigned Staff</th>
                <th>Created By</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($campaigns as $index => $campaign)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $campaign->campaign_name }}</td>
                    <td>{{ $campaign->campaign_code ?? '-' }}</td>
                    <td>{{ $campaign->platform->name ?? '-' }}</td>
                    <td>{{ $campaign->campaign_type ?? '-' }}</td>
                    <td>{{ $campaign->status ?? '-' }}</td>
                    <td>{{ $campaign->start_date ?? '-' }}</td>
                    <td>{{ $campaign->end_date ?? '-' }}</td>
                    <td>{{ number_format($campaign->budget_allocated ?? 0, 2) }}</td>
                    <td>{{ number_format($campaign->budget_spent ?? 0, 2) }}</td>
                    <td>{{ $campaign->assignedStaff->full_name ?? '-' }}</td>
                    <td>{{ $campaign->createdByStaff->full_name ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Generated on {{ now()->format('F j, Y, g:i a') }}
    </div>
</body>
</html>
