<!DOCTYPE html>
<html>
<head>
    <title>Lead Source Details</title>
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
        <p>Lead Source Details</p>
        <hr>
    </div>

    <table class="details-table">
        <tr>
            <th>Name:</th>
            <td>{{ $leadSource->name }}</td>
        </tr>
        <tr>
            <th>Category:</th>
            <td>{{ $leadSource->category }}</td>
        </tr>
        <tr>
            <th>Description:</th>
            <td>{{ $leadSource->description }}</td>
        </tr>
        <tr>
            <th>Active:</th>
            <td>{{ $leadSource->is_active ? 'Yes' : 'No' }}</td>
        </tr>
        <tr>
            <th>Created At:</th>
            <td>{{ \Carbon\Carbon::parse($leadSource->created_at)->format('M d, Y H:i') }}</td>
        </tr>
        <tr>
            <th>Updated At:</th>
            <td>{{ \Carbon\Carbon::parse($leadSource->updated_at)->format('M d, Y H:i') }}</td>
        </tr>
    </table>

    <div class="footer">
        <hr>
        <p>Document Generated: {{ \Carbon\Carbon::now()->format('M d, Y H:i') }}</p>
    </div>
</body>
</html>