<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Prescription - Geraye Healthcare</title>
  <style>
    body { 
      font-family: system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, sans-serif; 
      margin: 0; 
      padding: 20px; 
      background-color: #f1f5f9; 
      color: #0f172a; 
    }
    .container { 
      max-width: 800px; 
      margin: 0 auto; 
    }
    .card { 
      background: #ffffff; 
      border-radius: 12px; 
      padding: 30px; 
      box-shadow: 0 4px 6px rgba(0,0,0,0.05); 
      border: 1px solid #e2e8f0;
    }
    .header {
      text-align: center;
      padding-bottom: 20px;
      border-bottom: 1px solid #e2e8f0;
      margin-bottom: 25px;
    }
    .logo {
      max-width: 120px;
      margin: 0 auto 15px;
    }
    h1 { 
      font-size: 24px; 
      margin: 0 0 8px; 
      color: #0f172a;
      font-weight: 700;
    }
    .subtitle {
      font-size: 16px;
      color: #64748b;
      margin: 0;
    }
    .prescription-id {
      background: #dbeafe;
      color: #1e40af;
      padding: 4px 12px;
      border-radius: 20px;
      font-size: 14px;
      font-weight: 500;
      display: inline-block;
      margin-top: 10px;
    }
    .grid { 
      display: grid; 
      grid-template-columns: 1fr 1fr; 
      gap: 20px; 
      margin-bottom: 25px;
    }
    .detail-group {
      margin-bottom: 15px;
    }
    .label { 
      font-size: 13px; 
      color: #64748b; 
      margin-bottom: 4px; 
      text-transform: uppercase;
      letter-spacing: 0.5px;
      font-weight: 600;
    }
    .value { 
      font-size: 15px; 
      color: #0f172a; 
      font-weight: 500;
    }
    .instructions {
      background: #f8fafc;
      border: 1px solid #e2e8f0;
      border-radius: 8px;
      padding: 15px;
      margin: 20px 0;
    }
    .instructions .label {
      margin-top: 0;
    }
    .instructions .value {
      font-weight: normal;
      line-height: 1.6;
      white-space: pre-wrap;
    }
    table { 
      width: 100%; 
      border-collapse: collapse; 
      margin: 25px 0; 
    }
    th, td { 
      border: 1px solid #e2e8f0; 
      padding: 12px; 
      text-align: left; 
      font-size: 14px; 
    }
    th { 
      background: #f8fafc; 
      color: #0f172a;
      font-weight: 600;
    }
    tr:nth-child(even) {
      background-color: #f8fafc;
    }
    .actions { 
      margin-top: 30px; 
      padding-top: 25px;
      border-top: 1px solid #e2e8f0;
      display: flex; 
      gap: 12px; 
      flex-wrap: wrap; 
      align-items: center;
      justify-content: center;
    }
    .btn {
      display: inline-block; 
      padding: 10px 18px; 
      border-radius: 8px; 
      text-decoration: none; 
      font-weight: 500;
      font-size: 14px;
      text-align: center;
      transition: all 0.2s;
      border: none;
      cursor: pointer;
    }
    .btn-primary {
      background: linear-gradient(135deg, #0ea5e9, #0284c7);
      color: white;
      box-shadow: 0 2px 4px rgba(2, 132, 199, 0.2);
    }
    .btn-primary:hover {
      background: linear-gradient(135deg, #0284c7, #0ea5e9);
      transform: translateY(-1px);
    }
    .btn-secondary {
      background: #f1f5f9;
      color: #0f172a;
    }
    .btn-secondary:hover {
      background: #e2e8f0;
    }
    .btn-success {
      background: #22c55e;
      color: white;
    }
    .btn-info {
      background: #3b82f6;
      color: white;
    }
    .btn-warning {
      background: #f59e0b;
      color: white;
    }
    .status-badge {
      padding: 4px 12px;
      border-radius: 20px;
      font-size: 12px;
      font-weight: 500;
      text-transform: uppercase;
    }
    .status-draft {
      background: #fef3c7;
      color: #92400e;
    }
    .status-final {
      background: #dbeafe;
      color: #1e40af;
    }
    .status-dispensed {
      background: #d1fae5;
      color: #065f46;
    }
    .status-cancelled {
      background: #fee2e2;
      color: #991b1b;
    }
    .footer {
      text-align: center;
      margin-top: 30px;
      padding-top: 20px;
      border-top: 1px solid #e2e8f0;
      color: #64748b;
      font-size: 13px;
    }
    @media print {
      body {
        background: white;
        padding: 0;
      }
      .card {
        box-shadow: none;
        border: none;
      }
      .actions, .no-print {
        display: none !important;
      }
    }
  </style>
  <meta name="robots" content="noindex,nofollow">
</head>
<body>
  <div class="container">
    <div class="card">
      <div class="header">
        <div class="logo">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#0ea5e9" width="80" height="80">
            <path d="M11.645 20.91l-.007-.003-.022-.012a15.247 15.247 0 01-.383-.218 25.18 25.18 0 01-4.244-3.17C4.688 15.36 2.25 12.174 2.25 8.25 2.25 5.322 4.714 3 7.688 3A5.5 5.5 0 0112 5.052 5.5 5.5 0 0116.313 3c2.973 0 5.437 2.322 5.437 5.25 0 3.925-2.438 7.111-4.739 9.256a25.175 25.175 0 01-4.244 3.17 15.247 15.247 0 01-.383.219l-.022.012-.007.004-.003.001a.752.752 0 01-.704 0l-.003-.001z" />
          </svg>
        </div>
        <h1>Geraye Home Care Services</h1>
        <p class="subtitle">Prescription Details</p>
        <div class="prescription-id">Prescription #{{ $rx->id }}</div>
      </div>

      <div class="grid">
        <div class="detail-group">
          <div class="label">Patient</div>
          <div class="value">{{ $rx->patient->full_name ?? ('#'.$rx->patient_id) }}</div>
        </div>
        <div class="detail-group">
          <div class="label">Prescribed Date</div>
          <div class="value">{{ $rx->prescribed_date ? \Carbon\Carbon::parse($rx->prescribed_date)->format('F j, Y') : '-' }}</div>
        </div>
        <div class="detail-group">
          <div class="label">Doctor</div>
          <div class="value">{{ optional($rx->createdBy)->first_name }} {{ optional($rx->createdBy)->last_name }}</div>
        </div>
        <div class="detail-group">
          <div class="label">Status</div>
          <div class="value">
            <span class="status-badge status-{{ $rx->status ?? 'draft' }}">
              {{ ucfirst($rx->status ?? 'draft') }}
            </span>
          </div>
        </div>
      </div>

      @if($rx->instructions)
      <div class="instructions">
        <div class="label">Instructions</div>
        <div class="value">{{ $rx->instructions }}</div>
      </div>
      @endif

      <h2 style="color: #0f172a; margin: 25px 0 15px; font-size: 18px; font-weight: 600;">Medication Items</h2>
      <table>
        <thead>
          <tr>
            <th>Medication</th>
            <th>Dosage</th>
            <th>Frequency</th>
            <th>Duration</th>
            <th>Notes</th>
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
            <tr>
              <td colspan="5" style="text-align:center;color:#6b7280">No medication items found.</td>
            </tr>
          @endif
        </tbody>
      </table>

      <div class="actions">
        <a href="{{ route('public.prescriptions.pdf', ['token' => $rx->share_token]) }}" target="_blank" class="btn btn-primary">
          Download PDF
        </a>
        @php($shareUrl = route('public.prescriptions.show', ['token' => $rx->share_token]))
        <a href="https://wa.me/?text={{ rawurlencode('View your prescription from Geraye Healthcare: '.$shareUrl) }}" target="_blank" class="btn btn-success" rel="noopener">
          Share on WhatsApp
        </a>
        <a href="https://twitter.com/intent/tweet?text={{ rawurlencode('View your prescription from Geraye Healthcare') }}&url={{ rawurlencode($shareUrl) }}" target="_blank" class="btn btn-info" rel="noopener">
          Share on Twitter
        </a>
        <a href="https://t.me/share/url?url={{ rawurlencode($shareUrl) }}&text={{ rawurlencode('View your prescription from Geraye Healthcare') }}" target="_blank" class="btn btn-warning" rel="noopener">
          Share on Telegram
        </a>
        <button onclick="(async()=>{try{await navigator.clipboard.writeText('{{ $shareUrl }}');alert('Link copied to clipboard!')}catch(e){alert('Failed to copy link')}})()" class="btn btn-secondary">
          Copy Link
        </button>
      </div>

      <div class="footer">
        <p>This prescription was generated by Geraye Home Care Services.</p>
        <p class="no-print">Having trouble viewing this page? <a href="{{ $shareUrl }}" style="color: #0284c7;">Click here to refresh</a></p>
      </div>
    </div>
  </div>
</body>
</html>