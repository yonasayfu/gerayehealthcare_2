<x-printable-report
    title="Employee Insurance Record - {{ $employeeInsuranceRecord->id }}"
    :data="[
        ['label' => 'Patient ID', 'value' => $employeeInsuranceRecord->patient_id],
        ['label' => 'Policy ID', 'value' => $employeeInsuranceRecord->policy_id],
        ['label' => 'Kebele ID', 'value' => $employeeInsuranceRecord->kebele_id],
        ['label' => 'Woreda', 'value' => $employeeInsuranceRecord->woreda],
        ['label' => 'Region', 'value' => $employeeInsuranceRecord->region],
        ['label' => 'Federal ID', 'value' => $employeeInsuranceRecord->federal_id],
        ['label' => 'Ministry Department', 'value' => $employeeInsuranceRecord->ministry_department],
        ['label' => 'Employee ID Number', 'value' => $employeeInsuranceRecord->employee_id_number],
        ['label' => 'Verified', 'value' => $employeeInsuranceRecord->verified ? 'Yes' : 'No'],
        ['label' => 'Verified At', 'value' => $employeeInsuranceRecord->verified_at],
    ]"
    :columns="[
        ['key' => 'label', 'label' => 'Field', 'printWidth' => '30%'],
        ['key' => 'value', 'label' => 'Value', 'printWidth' => '70%'],
    ]"
    :header-info="[
        'logoSrc' => public_path('images/geraye_logo.jpeg'),
        'clinicName' => 'Geraye Home Care Services',
        'documentTitle' => 'Employee Insurance Record Details',
    ]"
    :footer-info="[
        'generatedDate' => true,
    ]"
/>