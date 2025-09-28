<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? ($headerInfo['document_title'] ?? 'Document') }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #111; margin-bottom: 80px; padding-bottom: 20px; }
        .header { text-align: center; margin-bottom: 16px; }
        .logo { max-width: 140px; max-height: 50px; display: block; margin: 0 auto 8px auto; }
        .clinic-name { font-weight: bold; font-size: 18px; margin: 0 0 4px 0; }
        .doc-title { font-size: 12px; color: #555; margin: 0; }
        table { width: 100%; border-collapse: collapse; font-size: 12px; }
        th, td { border: 1px solid #ccc; padding: 6px 8px; text-align: left; }
        thead { background: #f3f4f6; }
        .kv { margin-bottom: 6px; }
        .kv .label { font-weight: 600; margin-right: 6px; }
        /* Optional stamp at top-right */
        .stamp {
            position: fixed;
            top: 12px;
            right: 12px;
            z-index: 1200;
            opacity: 0.85;
            text-align: center;
        }
        .stamp img { max-width: 120px; max-height: 120px; }
        .stamp .stamp-text {
            display: inline-block;
            padding: 6px 10px;
            border: 2px solid #c53030;
            color: #c53030;
            font-weight: 700;
            font-size: 12px;
            transform: rotate(-12deg);
            background: rgba(255,255,255,0.85);
        }
        .footer { 
            position: fixed; 
            bottom: 0; 
            left: 0; 
            right: 0; 
            text-align: center; 
            font-size: 11px; 
            color: #666; 
            padding: 10px; 
            border-top: 1px solid #ccc; 
            background: white; 
            z-index: 1000;
            height: 40px;
            line-height: 20px;
        }
        .page-number:after {
            content: counter(page) " of " counter(pages);
        }
    </style>
</head>
<body>
    @if(!empty($headerInfo['stamp_image']) || !empty($headerInfo['stamp_text']))
        <div class="stamp">
            @if(!empty($headerInfo['stamp_image']) && file_exists($headerInfo['stamp_image']))
                <img src="{{ $headerInfo['stamp_image'] }}" alt="Stamp" />
            @elseif(!empty($headerInfo['stamp_text']))
                <span class="stamp-text">{{ $headerInfo['stamp_text'] }}</span>
            @endif
        </div>
    @endif
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
                            @elseif($key === 'staff_full_name')
                                <td>{{ ($row->staff->first_name ?? '') . ' ' . ($row->staff->last_name ?? '') ?: 'N/A' }}</td>
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

    @unless(!empty($hide_footer))
    <div class="footer">
        <div>Generated on: {{ $footerInfo['generated_date'] ?? now()->format('F j, Y, g:i a') }}</div>
        <div>Page <span class="page-number"></span></div>
        <div style="font-size: 10px; color: #888; margin-top: 2px;">Geraye Home Care Services - Confidential Document</div>
    </div>
    @endunless
</body>
</html>
