<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $data->invoice_number ?? '' }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #111; margin: 24px; }
        .header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px; }
        .header-left { display: flex; align-items: center; gap: 12px; }
        .logo { max-width: 140px; max-height: 50px; }
        .brand { display: flex; flex-direction: column; }
        .clinic-name { font-weight: bold; font-size: 18px; margin: 0; }
        .doc-title { font-size: 12px; color: #555; margin: 0; }
        .invoice-title { font-size: 24px; font-weight: 700; text-transform: uppercase; margin: 8px 0 0 0; text-align: right; }
        .meta { margin: 12px 0 24px; font-size: 12px; color: #444; }
        .section { margin: 18px 0; }
        .two-col { display: flex; justify-content: space-between; gap: 24px; }
        .card { border: 1px solid #e5e7eb; border-radius: 8px; padding: 12px 14px; }
        .muted { color: #6b7280; font-weight: 600; text-transform: uppercase; font-size: 11px; letter-spacing: .04em; }
        .bold { font-weight: 700; }
        table { width: 100%; border-collapse: collapse; font-size: 12px; }
        thead { background: #f3f4f6; }
        th, td { border: 1px solid #e5e7eb; padding: 8px 10px; text-align: left; }
        th.num, td.num { text-align: right; }
        .totals { margin-top: 12px; width: 320px; margin-left: auto; }
        .totals .row { display: flex; justify-content: space-between; padding: 6px 0; }
        .totals .grand { border-top: 1px solid #e5e7eb; margin-top: 6px; padding-top: 8px; font-size: 14px; font-weight: 700; }
        .footer { text-align: center; font-size: 10px; color: #888; margin-top: 28px; padding-top: 10px; border-top: 1px solid #e5e7eb; }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-left">
            @php $logoPath = public_path('images/geraye_logo.jpeg'); @endphp
            @if(file_exists($logoPath))
                <img class="logo" src="{{ $logoPath }}" alt="Geraye Logo">
            @endif
            <div class="brand">
                <div class="clinic-name">Geraye Home Care Services</div>
                <div class="doc-title">Invoice #{{ $data->invoice_number ?? '-' }}</div>
            </div>
        </div>
        <div class="invoice-title">INVOICE</div>
    </div>

    <div class="meta">
        <div>Generated on {{ now()->format('F j, Y, g:i a') }}</div>
    </div>

    <div class="section two-col">
        <div class="card" style="flex:1">
            <div class="muted">Bill To</div>
            <div class="bold">{{ data_get($data, 'patient.full_name', '-') }}</div>
            <div>{{ data_get($data, 'patient.address', '-') }}</div>
            <div>{{ data_get($data, 'patient.phone_number', '-') }}</div>
        </div>
        <div class="card" style="width: 300px;">
            <div class="row"><span class="muted">Invoice #</span> <span class="bold">{{ $data->invoice_number ?? '-' }}</span></div>
            <div class="row"><span class="muted">Invoice Date</span> <span>{{ optional($data->invoice_date)->format('F j, Y') ?? (\Carbon\Carbon::parse($data->invoice_date ?? null)->format('F j, Y') ?: '-') }}</span></div>
            <div class="row"><span class="muted">Due Date</span> <span>{{ optional($data->due_date)->format('F j, Y') ?? (\Carbon\Carbon::parse($data->due_date ?? null)->format('F j, Y') ?: '-') }}</span></div>
            <div class="row"><span class="muted">Status</span> <span>{{ $data->status ?? '-' }}</span></div>
        </div>
    </div>

    <div class="section">
        <table>
            <thead>
                <tr>
                    <th>Description</th>
                    <th class="num" style="width: 140px;">Amount</th>
                </tr>
            </thead>
            <tbody>
                @forelse(($data->items ?? []) as $item)
                    @php
                        $desc = $item->description
                            ?? data_get($item, 'visitService.description')
                            ?? data_get($item, 'visitService.name')
                            ?? 'Service';
                        $amt = $item->cost ?? $item->amount ?? 0;
                    @endphp
                    <tr>
                        <td>{{ $desc }}</td>
                        <td class="num">{{ number_format((float)$amt, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" style="text-align:center; color:#777;">No items</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="totals">
            <div class="row"><span>Subtotal</span><span>{{ number_format((float)($data->subtotal ?? 0), 2) }}</span></div>
            <div class="row"><span>Tax</span><span>{{ number_format((float)($data->tax_amount ?? 0), 2) }}</span></div>
            <div class="row grand"><span>Grand Total</span><span>{{ number_format((float)($data->grand_total ?? 0), 2) }}</span></div>
        </div>
    </div>

    <div class="footer">
        Geraye Home Care Services — Confidential Invoice
    </div>
</body>
</html>
