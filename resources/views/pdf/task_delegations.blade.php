<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Task Delegations Export – Geraye Home Care Services</title>
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
    <h1>Geraye Home Care Services</h1>
    <p>Task Delegations Export</p>
</header>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Title</th>
                <th>Assigned To</th>
                <th>Due Date</th>
                <th>Status</th>
                <th>Notes</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($tasks as $index => $t)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $t->title }}</td>
                    <td>{{ $t->assignee->first_name }} {{ $t->assignee->last_name }}</td>
                    <td>{{ \Carbon\Carbon::parse($t->due_date)->format('Y-m-d') }}</td>
                    <td>{{ $t->status }}</td>
                    <td>{{ $t->notes }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Exported on {{ now()->format('F j, Y, g:i a') }}
    </div>
</body>
</html>
