<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? ($headerInfo['document_title'] ?? 'Document') }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #111; }
        .header { text-align: center; margin-bottom: 16px; }
        .logo { max-width: 140px; max-height: 50px; display: block; margin: 0 auto 8px auto; }
        .clinic-name { font-weight: bold; font-size: 18px; margin: 0 0 4px 0; }
        .doc-title { font-size: 12px; color: #555; margin: 0; }
        table { width: 100%; border-collapse: collapse; font-size: 12px; }
        th, td { border: 1px solid #ccc; padding: 6px 8px; text-align: left; }
        thead { background: #f3f4f6; }
        .kv { margin-bottom: 6px; }
        .kv .label { font-weight: 600; margin-right: 6px; }
        .footer { position: fixed; bottom: 10px; left: 0; right: 0; text-align: center; font-size: 11px; color: #666; }
    </style>
</head>
<body>
    <div class="header">
        @if(!empty($headerInfo['logo']) && file_exists($headerInfo['logo']))
            <img class="logo" src="{{ $headerInfo['logo'] }}" alt="Logo">
        @endif
        <div class="clinic-name">{{ $headerInfo['clinic_name'] ?? 'Geraye Home Care Services' }}</div>
        <div class="doc-title">{{ $headerInfo['document_title'] ?? ($title ?? 'Document') }}</div>
    </div>

    @php
        $isCollection = is_iterable($data) && !($data instanceof \Illuminate\Contracts\Support\Arrayable && method_exists($data, 'toArray') && isset($data['id']));
    @endphp

    @if(isset($columns) && is_array($columns) && $isCollection)
        @php
            $indexBase = (is_object($data) && method_exists($data, 'firstItem') && $data->firstItem()) ? ($data->firstItem() - 1) : 0;
        @endphp
        <table>
            <thead>
                <tr>
                    @foreach($columns as $col)
                        <th>{{ $col['label'] ?? ucfirst($col['key'] ?? '') }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($data as $row)
                    <tr>
                        @foreach($columns as $col)
                            @php $key = $col['key'] ?? ''; @endphp
                            @if($key === 'index')
                                <td>{{ $indexBase + $loop->parent->iteration }}</td>
                            @else
                                <td>{{ data_get($row, $key, '-') }}</td>
                            @endif
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        @php $record = $data; @endphp
        @if(isset($columns) && is_array($columns))
            @foreach($columns as $col)
                @php $key = $col['key'] ?? ''; @endphp
                <div class="kv">
                    <span class="label">{{ $col['label'] ?? ucfirst($key) }}:</span>
                    <span class="value">{{ data_get($record, $key, '-') }}</span>
                </div>
            @endforeach
        @else
            <pre>{{ print_r($record, true) }}</pre>
        @endif
    @endif

    <div class="footer">
        Printed on: {{ $footerInfo['generated_date'] ?? now()->format('F j, Y, g:i a') }}
    </div>
</body>
</html>
