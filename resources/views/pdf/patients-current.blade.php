<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Patient List (Current View) - Geraye Home Care Services</title>
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
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            padding: 8px 10px;
            border: 1px solid #999;
            text-align: left;
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
    <h1 style="margin: 0;">Geraye Home Care Services</h1>
    <p style="margin: 0;">Patient List (Current View)</p>
</header>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Full Name</th>
                <th>Patient Code</th>
                <th>Fayda ID</th>
                <th>Age</th>
                <th>Gender</th>
                <th>Phone</th>
                <th>Source</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($patients as $index => $p)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $p->full_name }}</td>
                    <td>{{ $p->patient_code ?? '-' }}</td>
                    <td>{{ $p->fayda_id ?? '-' }}</td>
                    <td>{{ $p->date_of_birth ? \Carbon\Carbon::parse($p->date_of_birth)->age : '-' }}</td>
                    <td>{{ $p->gender ?? '-' }}</td>
                    <td>{{ $p->phone_number ?? '-' }}</td>
                    <td>{{ $p->source ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Generated on {{ now()->format('F j, Y, g:i a') }}
    </div>
</body>
</html>
