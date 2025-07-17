<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Staff Export - Geraye</title>
    <style>
        /* Print-specific styles adapted from PrintAll.vue */
        body {
            font-family: 'Segoe UI', sans-serif;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            color: #000 !important;
            margin: 0 !important;
            padding: 0 !important;
            overflow: visible !important;
            font-size: 12px; /* Base font size */
        }
        @page {
            size: A4 landscape;
            margin: 0.5cm;
        }

        .print-container {
            padding: 1cm;
            transform: scale(0.95);
            transform-origin: top left;
            width: 100%;
            height: auto;
        }

        .print-header-content {
            text-align: center;
            margin-bottom: 0.8cm;
        }
        .print-logo {
            max-width: 150px;
            max-height: 50px;
            margin-bottom: 0.5rem;
            display: block;
            margin-left: auto;
            margin-right: auto;
        }
        .print-clinic-name {
            font-size: 1.6rem !important;
            margin-bottom: 0.2rem !important;
            line-height: 1.2 !important;
            font-weight: bold;
        }
        .print-document-title {
            font-size: 0.85rem !important;
            color: #555 !important;
        }
        hr { border-color: #ccc !important; }

        .print-table-container {
            box-shadow: none !important;
            border-radius: 0 !important;
            overflow: visible !important;
        }
        table {
            width: 100% !important;
            border-collapse: collapse !important;
            font-size: 0.8rem !important; /* Table font size */
        }
        thead {
            background-color: #f0f0f0 !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        th, td {
            border: 1px solid #ddd !important;
            padding: 0.5rem 0.75rem !important;
            color: #000 !important;
            text-align: left; /* Ensure text alignment is left */
        }
        th {
            font-weight: bold !important;
            text-transform: uppercase !important;
            font-size: 0.75rem !important;
        }
        .print-table-row:nth-child(even) {
            background-color: #f9f9f9 !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        .print-table-row:last-child {
            border-bottom: 1px solid #ddd !important;
        }
        .print-footer {
            text-align: center;
            margin-top: 1cm;
            font-size: 0.7rem !important;
            color: #666 !important;
        }
        .print-footer hr {
            border-color: #ccc !important;
        }
    </style>
</head>
<body>
    <div class="print-container">
        <div class="print-header-content">
            <img src="{{ public_path('images/geraye_logo.jpeg') }}" alt="Geraye Logo" class="print-logo">
            <h1 class="print-clinic-name">Geraye Home-to-Home Care</h1>
            <p class="print-document-title">All Staff Records</p>
            <hr class="my-3 border-gray-300 print:my-2">
        </div>

        <div class="overflow-x-auto print-table-container">
            <table class="w-full text-left text-sm text-gray-800">
                <thead class="bg-gray-100 text-xs uppercase text-muted-foreground print-table-header">
                    <tr>
                        <th class="px-6 py-3">#</th>
                        <th class="px-6 py-3">Full Name</th>
                        <th class="px-6 py-3">Email</th>
                        <th class="px-6 py-3">Phone</th>
                        <th class="px-6 py-3">Position</th>
                        <th class="px-6 py-3">Department</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3">Hire Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($staff as $index => $s)
                        <tr class="border-b print-table-row">
                            <td class="px-6 py-4">{{ $index + 1 }}</td>
                            <td class="px-6 py-4">{{ $s->first_name }} {{ $s->last_name }}</td>
                            <td class="px-6 py-4">{{ $s->email ?? '-' }}</td>
                            <td class="px-6 py-4">{{ $s->phone ?? '-' }}</td>
                            <td class="px-6 py-4">{{ $s->position ?? '-' }}</td>
                            <td class="px-6 py-4">{{ $s->department ?? '-' }}</td>
                            <td class="px-6 py-4">{{ $s->status }}</td>
                            <td class="px-6 py-4">{{ \Carbon\Carbon::parse($s->hire_date)->format('Y-m-d') }}</td>
                        </tr>
                    @endforeach
                    @if (count($staff) === 0)
                        <tr>
                            <td colspan="8" class="text-center px-6 py-4 text-gray-400">No staff found.</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <div class="text-center mt-4 text-sm text-gray-500 print-footer">
            <hr class="my-2 border-gray-300">
