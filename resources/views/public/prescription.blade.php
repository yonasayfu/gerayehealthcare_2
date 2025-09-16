<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Prescription</title>
  <style>
    body { font-family: system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, sans-serif; margin: 2rem; color: #0f172a; }
    .card { max-width: 820px; margin: 0 auto; border: 1px solid #e5e7eb; border-radius: 12px; padding: 20px; box-shadow: 0 1px 3px rgba(0,0,0,.08); }
    h1 { font-size: 1.25rem; margin: 0 0 12px; }
    .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px 20px; }
    .label { font-size: 12px; color: #6b7280; margin-bottom: 2px; }
    .value { font-size: 14px; color: #111827; }
    table { width: 100%; border-collapse: collapse; margin-top: 16px; }
    th, td { border: 1px solid #e5e7eb; padding: 8px; text-align: left; font-size: 14px; }
    th { background: #f9fafb; }
    .actions a { display:inline-block; padding:8px 12px; border-radius:8px; background:#111827; color:#fff; text-decoration:none; }
  </style>
  <meta name="robots" content="noindex,nofollow">
</head>
<body>
  <div class="card">
    <h1>Prescription</h1>
    <div class="grid">
      <div>
        <div class="label">Patient</div>
        <div class="value">{{ $rx->patient->full_name ?? ('#'.$rx->patient_id) }}</div>
      </div>
      <div>
        <div class="label">Prescribed Date</div>
        <div class="value">{{ $rx->prescribed_date }}</div>
      </div>
      <div>
        <div class="label">Doctor</div>
        <div class="value">{{ optional($rx->createdBy)->first_name }} {{ optional($rx->createdBy)->last_name }}</div>
      </div>
      <div>
        <div class="label">Status</div>
        <div class="value">{{ ucfirst($rx->status) }}</div>
      </div>
      <div style="grid-column:1/3">
        <div class="label">Instructions</div>
        <div class="value">{{ $rx->instructions ?? '-' }}</div>
      </div>
    </div>
    <table>
      <thead>
        <tr>
          <th>Medication</th><th>Dosage</th><th>Frequency</th><th>Duration</th><th>Notes</th>
        </tr>
      </thead>
      <tbody>
        @foreach(($rx->items ?? []) as $it)
          <tr>
            <td>{{ $it->medication_name }}</td>
            <td>{{ $it->dosage }}</td>
            <td>{{ $it->frequency }}</td>
            <td>{{ $it->duration }}</td>
            <td>{{ $it->notes }}</td>
          </tr>
        @endforeach
        @if(($rx->items ?? collect())->count() === 0)
          <tr><td colspan="5" style="text-align:center;color:#6b7280">No items.</td></tr>
        @endif
      </tbody>
    </table>
    <div class="actions" style="margin-top:16px; display:flex; gap:8px; flex-wrap:wrap; align-items:center">
      <a href="{{ route('public.prescriptions.pdf', ['token' => $rx->share_token]) }}" target="_blank">Download PDF</a>
      @php($shareUrl = route('public.prescriptions.show', ['token' => $rx->share_token]))
      <a href="https://wa.me/?text={{ rawurlencode('View your prescription: '.$shareUrl) }}" target="_blank" style="background:#25D366" rel="noopener">Share on WhatsApp</a>
      <a href="https://twitter.com/intent/tweet?text={{ rawurlencode('View your prescription') }}&url={{ rawurlencode($shareUrl) }}" target="_blank" style="background:#1DA1F2" rel="noopener">Share on Twitter</a>
      <a href="https://t.me/share/url?url={{ rawurlencode($shareUrl) }}&text={{ rawurlencode('View your prescription') }}" target="_blank" style="background:#2AABEE" rel="noopener">Share on Telegram</a>
      <button onclick="(async()=>{try{await navigator.clipboard.writeText('{{ $shareUrl }}');alert('Link copied!')}catch(e){alert('Failed to copy link')}})()" style="background:#374151">Copy Link</button>
    </div>
  </div>
</body>
</html>
