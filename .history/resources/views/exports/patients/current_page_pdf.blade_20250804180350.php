<!DOCTYPE html>
<html>
<head>
    <title>{{ $document_title ?? 'Patient List (Current View)' }}</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid black; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h1>{{ $document_title ?? 'Patient List (Current View)' }}</h1>
    <table>
        <thead>
            <tr>
                @foreach ($columns ?? [] as $column)
                    <th>{{ $column['label'] }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($items ?? [] as $item)
                <tr>
                    @foreach ($columns ?? [] as $column)
                        <td>
                            @php
                                $value = data_get($item, $column['key']);
                                if (isset($column['transform']) && is_callable($column['transform'])) {
                                    echo $column['transform']($value, $item);
                                } else {
                                    echo $value;
                                }
                            @endphp
                        </td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
