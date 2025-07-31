<!DOCTYPE html>
<html>
<head>
    <title>Exchange Rate Details</title>
    <style>
        body { font-family: sans-serif; }
        .detail-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .detail-table th, .detail-table td { border: 1px solid black; padding: 8px; text-align: left; }
        .detail-table th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h1>Exchange Rate Details</h1>

    <table class="detail-table">
        <tr>
            <th>ID</th>
            <td>{{ $exchangeRate->id }}</td>
        </tr>
        <tr>
            <th>Currency Code</th>
            <td>{{ $exchangeRate->currency_code }}</td>
        </tr>
        <tr>
            <th>Rate to ETB</th>
            <td>{{ $exchangeRate->rate_to_etb }}</td>
        </tr>
        <tr>
            <th>Source</th>
            <td>{{ $exchangeRate->source }}</td>
        </tr>
        <tr>
            <th>Date Effective</th>
            <td>{{ $exchangeRate->date_effective }}</td>
        </tr>
        <tr>
            <th>Created At</th>
            <td>{{ $exchangeRate->created_at }}</td>
        </tr>
        <tr>
            <th>Updated At</th>
            <td>{{ $exchangeRate->updated_at }}</td>
        </tr>
    </table>
</body>
</html>
