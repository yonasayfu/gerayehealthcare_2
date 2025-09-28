<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Enter PIN</title>
  <style>
    body { font-family: system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, sans-serif; display:flex; align-items:center; justify-content:center; height:100vh; margin:0; background:#f8fafc; }
    .card { width:100%; max-width:420px; background:#fff; border:1px solid #e5e7eb; border-radius:12px; padding:24px; box-shadow: 0 1px 3px rgba(0,0,0,.08); }
    h1 { font-size: 1.125rem; margin: 0 0 8px; color:#111827; }
    p { margin: 0 0 16px; color:#6b7280; font-size:14px; }
    input { width:100%; padding:10px 12px; border:1px solid #d1d5db; border-radius:8px; font-size:14px; }
    button { margin-top:12px; width:100%; background:#06b6d4; color:#fff; padding:10px 12px; border:none; border-radius:8px; font-size:14px; cursor:pointer; }
    .error { color:#b91c1c; font-size:13px; margin-top:8px; }
  </style>
  <meta name="robots" content="noindex,nofollow">
</head>
<body>
  <div class="card">
    <h1>Protected Prescription</h1>
    <p>Please enter the PIN to view this prescription.</p>
    <form method="POST" action="{{ route('public.prescriptions.authenticate', ['token' => $token]) }}">
      @csrf
      <input type="text" name="pin" placeholder="Enter PIN" required />
      @error('pin')<div class="error">{{ $message }}</div>@enderror
      <button type="submit">Unlock</button>
    </form>
  </div>
</body>
</html>
