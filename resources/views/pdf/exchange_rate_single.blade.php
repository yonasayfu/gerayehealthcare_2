<x-printable-report
    title="Exchange Rate - {{ $exchangeRate->currency_code }}"
    :data="[
        ['label' => 'Currency Code', 'value' => $exchangeRate->currency_code],
        ['label' => 'Rate to ETB', 'value' => $exchangeRate->rate_to_etb],
        ['label' => 'Source', 'value' => $exchangeRate->source],
        ['label' => 'Date Effective', 'value' => $exchangeRate->date_effective],
    ]"
    :columns="[
        ['key' => 'label', 'label' => 'Field', 'printWidth' => '30%'],
        ['key' => 'value', 'label' => 'Value', 'printWidth' => '70%'],
    ]"
    :header-info="[
        'logoSrc' => public_path('images/geraye_logo.jpeg'),
        'clinicName' => 'Geraye Home Care Services',
        'documentTitle' => 'Exchange Rate Details',
    ]"
    :footer-info="[
        'generatedDate' => true,
    ]"
/>