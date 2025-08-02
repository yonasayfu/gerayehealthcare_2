<div id="app">
    <printable-report
        :title="{{ json_encode($title) }}"
        :data="{{ json_encode($data) }}"
        :columns="{{ json_encode($columns) }}"
        :header-info="{{ json_encode($headerInfo ?? []) }}"
        :footer-info="{{ json_encode($footerInfo ?? []) }}"
    ></printable-report>
</div>

@vite('resources/js/app.ts')
