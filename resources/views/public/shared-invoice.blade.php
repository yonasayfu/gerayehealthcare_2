<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Shared Invoice</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <style>
    body { font-family: system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, sans-serif; margin: 2rem; color: #0f172a; }
    .card { max-width: 760px; margin: 0 auto; border: 1px solid #e5e7eb; border-radius: 12px; padding: 20px; box-shadow: 0 1px 3px rgba(0,0,0,.08); }
    h1 { font-size: 1.25rem; margin: 0 0 12px; }
    .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px 20px; }
    .label { font-size: 12px; color: #6b7280; margin-bottom: 2px; }
    .value { font-size: 14px; color: #111827; }
    .badge { display: inline-block; padding: 2px 8px; border-radius: 9999px; font-size: 12px; font-weight: 600; }
    .badge-blue { background:#dbeafe; color:#1e40af; }
    .badge-amber { background:#fef3c7; color:#92400e; }
    .badge-green { background:#dcfce7; color:#166534; }
  </style>
  <meta name="robots" content="noindex,nofollow">
</head>
<body>
  <div class="card">
    <h1>Shared Invoice</h1>
    <div class="grid">
      <div>
        <div class="label">Invoice Number</div>
        <div class="value">{{ $invoice->invoice->invoice_number ?? 'N/A' }}</div>
      </div>
      <div>
        <div class="label">Partner</div>
        <div class="value">{{ $invoice->partner->name ?? 'N/A' }}</div>
      </div>
      <div>
        <div class="label">Share Date</div>
        <div class="value">{{ $invoice->share_date }}</div>
      </div>
      <div>
        <div class="label">Status</div>
        @php
          $status = $invoice->status;
          $cls = $status === 'Paid' ? 'badge badge-green' : ($status === 'Viewed' ? 'badge badge-amber' : 'badge badge-blue');
        @endphp
        <div class="value"><span class="{{ $cls }}">{{ $status }}</span></div>
      </div>
      <div>
        <div class="label">Shared By</div>
        <div class="value">{{ optional($invoice->sharedBy)->first_name }} {{ optional($invoice->sharedBy)->last_name }}</div>
      </div>
      <div>
        <div class="label">Notes</div>
        <div class="value">{{ $invoice->notes ?? '-' }}</div>
      </div>
    </div>
    <div style="margin-top:16px">
      @if(isset($invoice->invoice->id))
        <a href="{{ route('invoices.public_pdf', ['invoice' => $invoice->invoice->id]) }}" target="_blank" style="display:inline-block;padding:8px 12px;border-radius:8px;background:#111827;color:#fff;text-decoration:none;">Download PDF</a>
      @endif
    </div>
    <div style="margin-top:16px; display:flex; gap:8px; flex-wrap:wrap; align-items:center">
      @php($shareUrl = route('public.shared-invoices.show', ['token' => $invoice->share_token]))
      <a href="{{ route('invoices.public_pdf', ['invoice' => $invoice->invoice->id ?? null]) }}" target="_blank" style="display:inline-block;padding:8px 12px;border-radius:8px;background:#111827;color:#fff;text-decoration:none;">Download PDF</a>
      <a href="https://wa.me/?text={{ rawurlencode('View your invoice: '.$shareUrl) }}" target="_blank" style="display:inline-block;padding:8px 12px;border-radius:8px;background:#25D366;color:#fff;text-decoration:none;" rel="noopener">WhatsApp</a>
      <a href="https://twitter.com/intent/tweet?text={{ rawurlencode('View your invoice') }}&url={{ rawurlencode($shareUrl) }}" target="_blank" style="display:inline-block;padding:8px 12px;border-radius:8px;background:#1DA1F2;color:#fff;text-decoration:none;" rel="noopener">Twitter</a>
      <a href="https://t.me/share/url?url={{ rawurlencode($shareUrl) }}&text={{ rawurlencode('View your invoice') }}" target="_blank" style="display:inline-block;padding:8px 12px;border-radius:8px;background:#2AABEE;color:#fff;text-decoration:none;" rel="noopener">Telegram</a>
      <button onclick="(async()=>{try{await navigator.clipboard.writeText('{{ $shareUrl }}');alert('Link copied!')}catch(e){alert('Failed to copy link')}})()" style="display:inline-block;padding:8px 12px;border-radius:8px;background:#374151;color:#fff;border:none;cursor:pointer;">Copy Link</button>
    </div>
  </div>
</body>
<script>
  // Prevent being indexed or embedded without consent
  if (window.top !== window.self) {
    window.top.location = window.location;
  }
  document.addEventListener('keydown', function(e){ if ((e.ctrlKey||e.metaKey) && e.key === 'p') e.preventDefault(); });
</script>
</html>
