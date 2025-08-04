<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Report' }}</title>
    @vite(['resources/css/app.css', 'resources/css/print.css'])
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
