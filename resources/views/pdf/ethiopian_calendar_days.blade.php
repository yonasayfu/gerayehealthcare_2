<x-printable-report
    title="Ethiopian Calendar Days List"
    :data="$ethiopianCalendarDays->map(function($day) {
        return [
            'gregorian_date' => $day->gregorian_date,
            'ethiopian_date' => $day->ethiopian_date,
            'description' => $day->description,
            'is_holiday' => $day->is_holiday ? 'Yes' : 'No',
        ];
    })->toArray()"
    :columns="[
        ['key' => 'gregorian_date', 'label' => 'Gregorian Date'],
        ['key' => 'ethiopian_date', 'label' => 'Ethiopian Date'],
        ['key' => 'description', 'label' => 'Description'],
        ['key' => 'is_holiday', 'label' => 'Is Holiday'],
    ]"
    :header-info="[
        'logoSrc' => public_path('images/geraye_logo.jpeg'),
        'clinicName' => 'Geraye Home Care Services',
        'documentTitle' => 'Ethiopian Calendar Days List',
    ]"
    :footer-info="[
        'generatedDate' => true,
    ]"
/>