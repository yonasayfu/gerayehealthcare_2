<!DOCTYPE html>
<html>
<head>
    <title>Campaign Performance Report</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10px;
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
        h1 {
            font-size: 18px;
            text-align: center;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <h1>Campaign Performance Report</h1>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Impressions</th>
                <th>Clicks</th>
                <th>Conversions</th>
                <th>Revenue Generated</th>
                <th>Total Cost</th>
            </tr>
        </thead>
        <tbody>
            @foreach($performanceData as $data)
            <tr>
                <td>{{ $data->date }}</td>
                <td>{{ $data->impressions }}</td>
                <td>{{ $data->clicks }}</td>
                <td>{{ $data->conversions }}</td>
                <td>{{ $data->revenue_generated }}</td>
                <td>{{ $data->total_cost }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
