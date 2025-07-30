<!DOCTYPE html>
<html>
<head>
    <title>Marketing Tasks</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10px;
            margin: 0.5cm;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 5px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header img {
            max-width: 100px;
            margin-bottom: 10px;
        }
        .header h1 {
            font-size: 18px;
            margin: 0;
        }
        .header p {
            font-size: 12px;
            margin: 0;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 9px;
            position: fixed;
            bottom: 0.5cm;
            width: 100%;
            left: 0;
            right: 0;
        }
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="data:image/jpeg;base64,{{ base64_encode(file_get_contents(public_path('images/geraye_logo.jpeg'))) }}" alt="Geraye Logo">
        <h1>Geraye Home Care Services</h1>
        <p>Marketing Tasks List</p>
        <hr>
    </div>

    <table>
        <thead>
            <tr>
                <th>Task Code</th>
                <th>Title</th>
                <th>Campaign</th>
                <th>Assigned To</th>
                <th>Type</th>
                <th>Status</th>
                <th>Scheduled At</th>
            </tr>
        </thead>
        <tbody>
            @foreach($marketingTasks as $task)
                <tr>
                    <td>{{ $task->task_code }}</td>
                    <td>{{ $task->title }}</td>
                    <td>{{ $task->campaign->campaign_name ?? '-' }}</td>
                    <td>{{ $task->assignedToStaff->user->name ?? '-' }}</td>
                    <td>{{ $task->task_type }}</td>
                    <td>{{ $task->status }}</td>
                    <td>{{ $task->scheduled_at ? $task->scheduled_at->format('Y-m-d H:i') : '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <hr>
        <p>Document Generated: {{ \Carbon\Carbon::now()->format('M d, Y H:i:s') }}</p>
    </div>
</body>
</html>