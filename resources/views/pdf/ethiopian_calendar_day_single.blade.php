<x-printable-report
    title="Ethiopian Calendar Day - {{ $ethiopianCalendarDay->gregorian_date }}"
    :data="[
        ['label' => 'Gregorian Date', 'value' => $ethiopianCalendarDay->gregorian_date],
        ['label' => 'Ethiopian Date', 'value' => $ethiopianCalendarDay->ethiopian_date],
        ['label' => 'Description', 'value' => $ethiopianCalendarDay->description],
        ['label' => 'Is Holiday', 'value' => $ethiopianCalendarDay->is_holiday ? 'Yes' : 'No'],
        ['label' => 'Region', 'value' => $ethiopianCalendarDay->region],
    ]"
    :columns="[
        ['key' => 'label', 'label' => 'Field', 'printWidth' => '30%'],
        ['key' => 'value', 'label' => 'Value', 'printWidth' => '70%'],
    ]"
    :header-info="[
        'logoSrc' => public_path('images/geraye_logo.jpeg'),
        'clinicName' => 'Geraye Home Care Services',
        'documentTitle' => 'Ethiopian Calendar Day Details',
    ]"
    :footer-info="[
        'generatedDate' => true,
    ]"
/>