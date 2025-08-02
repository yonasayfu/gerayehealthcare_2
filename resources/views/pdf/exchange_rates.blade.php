<x-printable-report
    title="Exchange Rates List - Geraye Home Care Services"
    :data="$exchangeRates->map(function($rate) {
        return [
            'currency_code' => $rate->currency_code,
            'rate_to_etb' => $rate->rate_to_etb,
            'source' => $rate->source,
            'date_effective' => $rate->date_effective,
        ];
    })->toArray()"
    :columns="[
        ['key' => 'currency_code', 'label' => 'Currency Code'],
        ['key' => 'rate_to_etb', 'label' => 'Rate to ETB'],
        ['key' => 'source', 'label' => 'Source'],
        ['key' => 'date_effective', 'label' => 'Date Effective'],
    ]"
    :header-info="[
        'logoSrc' => public_path('images/geraye_logo.jpeg'),
        'clinicName' => 'Geraye Home Care Services',
        'documentTitle' => 'Exchange Rates List',
    ]"
    :footer-info="[
        'generatedDate' => true,
    ]"
/>