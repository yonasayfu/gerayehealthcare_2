<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Report' }}</title>
    <!-- Remove @vite directive and add inline styles for PDF generation -->
    <style>
        /* Page size and margins for PDF/Print */
        @page {
            size: A4 landscape; /* Make print-all and print-current landscape */
            margin: 10mm;
        }
        /* Reset and base styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            background: white;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        
        /* Header styles */
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #333;
        }
        
        .logo {
            max-width: 150px;
            max-height: 50px;
            margin: 0 auto 10px;
            display: block;
        }
        
        .clinic-name {
            font-size: 18px;
            font-weight: bold;
            margin: 5px 0;
            color: #333;
        }
        
        .document-title {
            font-size: 14px;
            color: #666;
            margin-bottom: 5px;
        }
        
        /* Table styles */
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
            font-size: 11px;
            table-layout: fixed; /* helps long content wrap nicely in landscape */
            word-wrap: break-word;
        }
        
        th, td {
            border: 1px solid #ddd;
            padding: 6px 8px;
            text-align: left;
            vertical-align: top;
            overflow-wrap: anywhere;
        }
        
        th {
            background-color: #f5f5f5;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 10px;
        }
        
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        /* Footer styles */
        .footer {
            text-align: center;
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
            font-size: 10px;
            color: #666;
        }
        
        /* Print-specific styles */
        @media print {
            body {
                margin: 0;
                padding: 6mm;
            }

            .header {
                margin-bottom: 12px;
            }

            table {
                page-break-inside: avoid;
            }

            th, td {
                padding: 4px 6px;
            }

            /* Avoid orphan headers/rows */
            thead { display: table-header-group; }
            tfoot { display: table-footer-group; }
        }
        
        /* Utility classes */
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .font-bold { font-weight: bold; }
        .text-sm { font-size: 10px; }
        .text-xs { font-size: 9px; }
        .mb-2 { margin-bottom: 8px; }
        .mb-4 { margin-bottom: 16px; }
        .mt-4 { margin-top: 16px; }
        .p-4 { padding: 16px; }
    </style>
</head>
<body>
    <div id="app">
        <x-printable-report
            :title="$title ?? 'Report'"
            :data="$data"
            :columns="$columns"
            :header-info="$headerInfo ?? []"
            :footer-info="$footerInfo ?? []"
        />
    </div>
</body>
</html>
