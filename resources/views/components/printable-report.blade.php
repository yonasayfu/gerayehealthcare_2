<div class="printable-report-container">
    <div class="print-header">
        @if (!empty($headerInfo))
            @if (isset($headerInfo['logo']))
                <img src="{{ $headerInfo['logo'] }}" alt="Logo" class="print-logo">
            @endif
            @if (isset($headerInfo['clinic_name']))
                <h1 class="print-clinic-name">{{ $headerInfo['clinic_name'] }}</h1>
            @endif
            @if (isset($headerInfo['document_title']))
                <p class="print-document-title">{{ $headerInfo['document_title'] }}</p>
            @endif
            <hr class="print-header-hr">
        @endif
    </div>

    <div class="print-body">
        @if (isset($data) && !empty($data))
            @if (is_array($data) || $data instanceof \Illuminate\Support\Collection)
                {{-- Handle multiple records (table view) --}}
                @if (count($data) > 0)
                    <table class="print-table">
                        <thead>
                            <tr>
                                @if (isset($columns) && is_array($columns))
                                    @foreach ($columns as $column)
                                        <th>{{ $column['label'] ?? $column['key'] ?? 'Column' }}</th>
                                    @endforeach
                                @else
                                    {{-- Fallback: use first record to generate headers --}}
                                    @php $firstItem = $data->first() ?? $data[0] ?? null; @endphp
                                    @if ($firstItem)
                                        @foreach ($firstItem->toArray() as $key => $value)
                                            <th>{{ ucwords(str_replace('_', ' ', $key)) }}</th>
                                        @endforeach
                                    @endif
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data as $index => $item)
                                <tr>
                                    @if (isset($columns) && is_array($columns))
                                        @foreach ($columns as $column)
                                            <td>
                                                @php
                                                    $value = data_get($item, $column['key']);
                                                    if (isset($column['transform']) && is_callable($column['transform'])) {
                                                        echo $column['transform']($value, $item);
                                                    } else {
                                                        echo $value ?? '-';
                                                    }
                                                @endphp
                                            </td>
                                        @endforeach
                                    @else
                                        {{-- Fallback: display all fields --}}
                                        @foreach ($item->toArray() as $key => $value)
                                            <td>{{ $value ?? '-' }}</td>
                                        @endforeach
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p class="no-data-message">No records found.</p>
                @endif
            @elseif (is_object($data))
                {{-- Handle single record display --}}
                <div class="single-record-details">
                    @if (isset($columns) && is_array($columns))
                        @foreach ($columns as $column)
                            <div class="detail-item">
                                <span class="detail-label">{{ $column['label'] ?? $column['key'] ?? 'Field' }}:</span>
                                <span class="detail-value">
                                    @php
                                        $value = data_get($data, $column['key']);
                                        if (isset($column['transform']) && is_callable($column['transform'])) {
                                            echo $column['transform']($value, $data);
                                        } else {
                                            echo $value ?? '-';
                                        }
                                    @endphp
                                </span>
                            </div>
                        @endforeach
                    @else
                        {{-- Fallback: display all fields --}}
                        @foreach ($data->toArray() as $key => $value)
                            <div class="detail-item">
                                <span class="detail-label">{{ ucwords(str_replace('_', ' ', $key)) }}:</span>
                                <span class="detail-value">{{ $value ?? '-' }}</span>
                            </div>
                        @endforeach
                    @endif
                </div>
            @else
                <p class="no-data-message">Invalid data format.</p>
            @endif
        @else
            <p class="no-data-message">No data available for this report.</p>
        @endif
    </div>

    <div class="print-footer">
        @if (!empty($footerInfo))
            <hr class="print-footer-hr">
            @if (isset($footerInfo['generated_date']))
                <p class="print-generated-date">Document Generated: {{ $footerInfo['generated_date'] }}</p>
            @endif
        @endif
    </div>
</div>

<style>
    .printable-report-container {
        padding: 20px;
    }
    
    .print-header {
        text-align: center;
        margin-bottom: 30px;
    }
    
    .print-logo {
        max-width: 150px;
        max-height: 50px;
        margin-bottom: 10px;
    }
    
    .print-clinic-name {
        font-size: 18px;
        font-weight: bold;
        margin: 5px 0;
    }
    
    .print-document-title {
        font-size: 14px;
        color: #666;
        margin-bottom: 10px;
    }
    
    .print-header-hr {
        border: none;
        border-top: 2px solid #333;
        margin: 10px 0;
    }
    
    .print-table {
        width: 100%;
        border-collapse: collapse;
        margin: 20px 0;
    }
    
    .print-table th,
    .print-table td {
        border: 1px solid #ddd;
        padding: 8px;
        text-align: left;
    }
    
    .print-table th {
        background-color: #f5f5f5;
        font-weight: bold;
    }

    /* Zebra striping for readability */
    .print-table tbody tr:nth-child(odd) {
        background-color: #fafafa;
    }

    .print-table tbody tr:hover {
        background-color: #f0f0f0;
    }
    
    .single-record-details {
        margin: 20px 0;
    }
    
    .detail-item {
        margin-bottom: 10px;
        display: flex;
        border-bottom: 1px solid #eee;
        padding-bottom: 5px;
    }
    
    .detail-label {
        font-weight: bold;
        min-width: 150px;
        margin-right: 10px;
    }
    
    .detail-value {
        flex: 1;
    }
    
    .no-data-message {
        text-align: center;
        color: #666;
        font-style: italic;
        margin: 40px 0;
    }
    
    .print-footer {
        margin-top: 30px;
        text-align: center;
    }
    
    .print-footer-hr {
        border: none;
        border-top: 1px solid #ddd;
        margin: 10px 0;
    }
    
    .print-generated-date {
        font-size: 12px;
        color: #666;
    }

    /* Print-optimized rules */
    @media print {
        @page {
            size: A4;
            margin: 1cm;
        }

        html, body {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        .printable-report-container {
            padding: 0;
        }

        .print-header-hr,
        .print-footer-hr {
            border-top-color: #bbb !important;
        }

        .print-header { margin-bottom: 16px; }
        .print-document-title { margin-bottom: 6px; }

        /* Avoid row breaks */
        .print-table tr { page-break-inside: avoid; }
        .single-record-details, .detail-item { page-break-inside: avoid; }

        /* Fixed footer at bottom of page */
        .print-footer {
            position: fixed;
            bottom: 0.5cm;
            left: 0;
            right: 0;
            background: #fff;
            padding-top: 6px;
        }
    }
</style>