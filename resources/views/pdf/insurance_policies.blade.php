<!DOCTYPE html>
<html>
<head>
    <title>Insurance Policies List - Geraye Home Care Services</title>
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
        <p>Insurance Policies List</p>
    </header>

    <table>
        <thead>
            <tr>
                <th>Service Type</th>
                <th>Coverage Percentage</th>
                <th>Coverage Type</th>
                <th>Is Active</th>
            </tr>
        </thead>
        <tbody>
            @foreach($insurancePolicies as $policy)
            <tr>
                <td>{{ $policy->service_type }}</td>
                <td>{{ $policy->coverage_percentage }}</td>
                <td>{{ $policy->coverage_type }}</td>
                <td>{{ $policy->is_active ? 'Yes' : 'No' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Generated on {{ now()->format('F j, Y, g:i a') }}
    </div>
</body>
</html>
