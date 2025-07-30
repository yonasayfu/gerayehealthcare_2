<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Landing Pages Export - Geraye Home Care Services</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            font-size: 12px;
            color: #333;
        }
        header {
            text-align: center;
            margin-bottom: 30px;
        }
        h1 {
            font-size: 20px;
            margin: 0;
        }
        p {
            font-size: 14px;
            margin: 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            padding: 8px 10px;
            border: 1px solid #999;
            text-align: left;
        }
        th {
            background-color: #f3f3f3;
        }
        .footer {
            text-align: right;
            margin-top: 30px;
            font-size: 11px;
        }
    </style>
</head>
<body>
   <header style="text-align: center; margin-bottom: 30px;">
    <img src="{{ public_path('images/geraye_logo.jpeg') }}" alt="Geraye Logo" style="max-height: 60px; margin-bottom: 10px;">
    <h1 style="margin: 0;">Geraye Home Care Services</h1>
    <p style="margin: 0;">Landing Pages Export</p>
</header>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Page Title</th>
                <th>Page URL</th>
                <th>Page Code</th>
                <th>Campaign</th>
                <th>Active</th>
                <th>Language</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($pages as $index => $page)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $page->page_title }}</td>
                    <td>{{ $page->page_url }}</td>
                    <td>{{ $page->page_code }}</td>
                    <td>{{ $page->campaign->campaign_name ?? '-' }}</td>
                    <td>{{ $page->is_active ? 'Yes' : 'No' }}</td>
                    <td>{{ $page->language }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Exported on {{ now()->format('F j, Y, g:i a') }}
    </div>
</body>
</html>
