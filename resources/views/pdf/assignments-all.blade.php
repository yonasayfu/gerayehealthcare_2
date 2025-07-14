<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>All Caregiver Assignments - Geraye</title>
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
    <h1 style="margin: 0;">Geraye Home-to-Home Care</h1>
    <p style="margin: 0;">Caregiver Assignment Records Export</p>
</header>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Patient Name</th>
                <th>Staff Member</th>
                <th>Shift Start</th>
                <th>Shift End</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($assignments as $index => $assignment)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $assignment->patient->full_name ?? 'N/A' }}</td>
                    <td>{{ ($assignment->staff->first_name ?? '') . ' ' . ($assignment->staff->last_name ?? '') }}</td>
                    <td>{{ $assignment->shift_start ? \Carbon\Carbon::parse($assignment->shift_start)->format('F j, Y, g:i a') : 'N/A' }}</td>
                    <td>{{ $assignment->shift_end ? \Carbon\Carbon::parse($assignment->shift_end)->format('F j, Y, g:i a') : 'N/A' }}</td>
                    <td>{{ $assignment->status }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Generated on {{ now()->format('F j, Y, g:i a') }}
    </div>
</body>
</html>
