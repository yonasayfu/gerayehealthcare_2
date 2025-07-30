<!DOCTYPE html>
<html>
<head>
    <title>Lead Sources List</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10px;
            margin: 0.5cm;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 5px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header img {
            max-width: 100px;
            margin-bottom: 10px;
        }
        .header h1 {
            font-size: 18px;
            margin: 0;
        }
        .header p {
            font-size: 12px;
            margin: 0;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 9px;
            position: fixed;
            bottom: 0.5cm;
            width: 100%;
            left: 0;
            right: 0;
        }
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="data:image/jpeg;base64,{{ base64_encode(file_get_contents(public_path('images/geraye_logo.jpeg'))) }}" alt="Geraye Logo">
        <h1>Geraye Home Care Services</h1>
        <p>Lead Sources List</p>
        <hr>
    </div>

    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Category</th>
                <th>Active</th>
            </tr>
        </thead>
        <tbody>
            @foreach($leadSources as $source)
            <tr>
                <td>{{ $source->name }}</td>
                <td>{{ $source->category ?? '-' }}</td>
                <td>{{ $source->is_active ? 'Yes' : 'No' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <hr>
        <p>Document Generated: {{ \Carbon\Carbon::now()->format('M d, Y H:i:s') }}</p>
    </div>
</body>
</html>
