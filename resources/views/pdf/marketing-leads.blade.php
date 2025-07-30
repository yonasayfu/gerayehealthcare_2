<!DOCTYPE html>
<html>
<head>
    <title>Marketing Leads List</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10px;
            margin: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header img {
            max-width: 150px;
            margin-bottom: 10px;
        }
        .header h1 {
            font-size: 20px;
            margin: 0;
        }
        .header p {
            font-size: 12px;
            margin: 0;
            color: #555;
        }
        .footer {
            text-align: center;
            font-size: 9px;
            color: #777;
            margin-top: 30px;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ public_path('images/geraye_logo.jpeg') }}" alt="Geraye Logo">
        <h1>Geraye Home Care Services</h1>
        <p>Marketing Leads List</p>
        <hr>
    </div>

    <table>
        <thead>
            <tr>
                <th>Lead Code</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Status</th>
                <th>Source Campaign</th>
                <th>Landing Page</th>
                <th>Assigned Staff</th>
            </tr>
        </thead>
        <tbody>
            @foreach($leads as $lead)
            <tr>
                <td>{{ $lead->lead_code }}</td>
                <td>{{ $lead->first_name }} {{ $lead->last_name }}</td>
                <td>{{ $lead->email }}</td>
                <td>{{ $lead->phone_number }}</td>
                <td>{{ $lead->status }}</td>
                <td>{{ $lead->sourceCampaign->name ?? 'N/A' }}</td>
                <td>{{ $lead->landingPage->name ?? 'N/A' }}</td>
                <td>{{ $lead->assignedStaff->user->name ?? 'N/A' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <hr>
        <p>Document Generated: {{ \Carbon\Carbon::now()->format('M d, Y H:i') }}</p>
    </div>
</body>
</html>