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
        @if (isset($data) && is_array($data) && count($data) > 0)
            <table class="print-table">
                <thead>
                    <tr>
                        @foreach ($columns as $column)
                            <th>{{ $column['label'] }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data as $item)
                        <tr>
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
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @elseif (isset($data) && is_object($data))
            {{-- Handle single record display --}}
            <div class="single-record-details">
                @foreach ($columns as $column)
                    <div class="detail-item">
                        <span class="detail-label">{{ $column['label'] }}:</span>
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
            </div>
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