<!DOCTYPE html>
<html>
<head>
    <title>Marketing Lead Details</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            margin: 20px;
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
            font-size: 24px;
            margin: 0;
        }
        .header p {
            font-size: 14px;
            margin: 0;
            color: #555;
        }
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .details-table th, .details-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .details-table th {
            background-color: #f2f2f2;
            width: 30%;
        }
        .footer {
            text-align: center;
            font-size: 10px;
            color: #777;
            margin-top: 30px;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ public_path('images/geraye_logo.jpeg') }}" alt="Geraye Logo">
        <h1>Geraye Home Care Services</h1>
        <p>Marketing Lead Details</p>
        <hr>
    </div>

    <table class="details-table">
        <tr>
            <th>Lead Code:</th>
            <td>{{ $lead->lead_code }}</td>
        </tr>
        <tr>
            <th>Full Name:</th>
            <td>{{ $lead->first_name }} {{ $lead->last_name }}</td>
        </tr>
        <tr>
            <th>Email:</th>
            <td>{{ $lead->email }}</td>
        </tr>
        <tr>
            <th>Phone Number:</th>
            <td>{{ $lead->phone_number }}</td>
        </tr>
        <tr>
            <th>Status:</th>
            <td>{{ $lead->status }}</td>
        </tr>
        <tr>
            <th>Source Campaign:</th>
            <td>{{ $lead->sourceCampaign->name ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th>Landing Page:</th>
            <td>{{ $lead->landingPage->name ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th>Assigned Staff:</th>
            <td>{{ $lead->assignedStaff->user->name ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th>Conversion Date:</th>
            <td>{{ $lead->conversion_date ? \Carbon\Carbon::parse($lead->conversion_date)->format('M d, Y') : 'N/A' }}</td>
        </tr>
        <tr>
            <th>Converted Patient:</th>
            <td>{{ $lead->convertedPatient->full_name ?? 'N/A' }} ({{ $lead->convertedPatient->patient_code ?? 'N/A' }})</td>
        </tr>
        <tr>
            <th>Notes:</th>
            <td>{{ $lead->notes ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th>Created At:</th>
            <td>{{ \Carbon\Carbon::parse($lead->created_at)->format('M d, Y H:i') }}</td>
        </tr>
        <tr>
            <th>Updated At:</th>
            <td>{{ \Carbon\Carbon::parse($lead->updated_at)->format('M d, Y H:i') }}</td>
        </tr>
    </table>

    <div class="footer">
        <hr>
        <p>Document Generated: {{ \Carbon\Carbon::now()->format('M d, Y H:i') }}</p>
    </div>
</body>
</html>