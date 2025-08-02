<x-printable-report
    title="Employee Insurance Records List - Geraye Home Care Services"
    :data="$employeeInsuranceRecords->map(function($record) {
        return [
            'kebele_id' => $record->kebele_id,
            'woreda' => $record->woreda,
            'region' => $record->region,
            'federal_id' => $record->federal_id,
            'ministry_department' => $record->ministry_department,
            'employee_id_number' => $record->employee_id_number,
            'verified' => $record->verified ? 'Yes' : 'No',
        ];
    })->toArray()"
    :columns="[
        ['key' => 'kebele_id', 'label' => 'Kebele ID'],
        ['key' => 'woreda', 'label' => 'Woreda'],
        ['key' => 'region', 'label' => 'Region'],
        ['key' => 'federal_id', 'label' => 'Federal ID'],
        ['key' => 'ministry_department', 'label' => 'Ministry Department'],
        ['key' => 'employee_id_number', 'label' => 'Employee ID Number'],
        ['key' => 'verified', 'label' => 'Verified'],
    ]"
    :header-info="[
        'logoSrc' => public_path('images/geraye_logo.jpeg'),
        'clinicName' => 'Geraye Home Care Services',
        'documentTitle' => 'Employee Insurance Records List',
    ]"
    :footer-info="[
        'generatedDate' => true,
    ]"
/>