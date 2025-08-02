<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Report' }}</title>
    <link rel="stylesheet" href="{{ public_path('build/assets/app.css') }}">
    <style>
        /* Ensure print styles from print.css are applied */
        @import "{{ public_path('build/assets/print.css') }}";
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
