<!DOCTYPE html>
<html>
<head>
    <title>Exchange Rates</title>
    <style>
        body { font-family: sans-serif; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid black; padding: 8px; text-align: left; }
    </style>
</head>
<body>
    <h1>Exchange Rates</h1>
    <table>
        <thead>
            <tr>
                <th>Currency Code</th>
                <th>Rate to ETB</th>
                <th>Source</th>
                <th>Date Effective</th>
            </tr>
        </thead>
        <tbody>
            @foreach($exchangeRates as $rate)
            <tr>
                <td>{{ $rate->currency_code }}</td>
                <td>{{ $rate->rate_to_etb }}</td>
                <td>{{ $rate->source }}</td>
                <td>{{ $rate->date_effective }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
