<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Staff Export - Geraye</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
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
    <header>
        <img src="{{ public_path('images/geraye_logo.jpeg') }}" alt="Geraye Logo" style="max-height: 60px; margin-bottom: 10px;">
        <h1>Geraye Home-to-Home Care</h1>
        <p>Staff Records Export</p>
    </header>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Full Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Position</th>
                <th>Department</th>
                <th>Status</th>
                <th>Hire Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($staff as $index => $s)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $s->first_name }} {{ $s->last_name }}</td>
                    <td>{{ $s->email ?? '-' }}</td>
                    <td>{{ $s->phone ?? '-' }}</td>
                    <td>{{ $s->position ?? '-' }}</td>
                    <td>{{ $s->department ?? '-' }}</td>
                    <td>{{ $s->status }}</td>
                    <td>{{ \Carbon\Carbon::parse($s->hire_date)->format('Y-m-d') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Exported on {{ now()->format('F j, Y, g:i a') }}
    </div>
</body>
</html>
