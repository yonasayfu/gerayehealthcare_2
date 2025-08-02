<x-printable-report
    title="Landing Pages Export - Geraye Home Care Services"
    :data="$pages->map(function($page, $index) {
        return [
            'index' => $index + 1,
            'page_title' => $page->page_title,
            'page_url' => $page->page_url,
            'page_code' => $page->page_code,
            'campaign_name' => $page->campaign->campaign_name ?? '-',
            'is_active' => $page->is_active ? 'Yes' : 'No',
            'language' => $page->language,
        ];
    })->toArray()"
    :columns="[
        ['key' => 'index', 'label' => '#'],
        ['key' => 'page_title', 'label' => 'Page Title'],
        ['key' => 'page_url', 'label' => 'Page URL'],
        ['key' => 'page_code', 'label' => 'Page Code'],
        ['key' => 'campaign_name', 'label' => 'Campaign'],
        ['key' => 'is_active', 'label' => 'Active'],
        ['key' => 'language', 'label' => 'Language'],
    ]"
    :header-info="[
        'logoSrc' => public_path('images/geraye_logo.jpeg'),
        'clinicName' => 'Geraye Home Care Services',
        'documentTitle' => 'Landing Pages Export',
    ]"
    :footer-info="[
        'generatedDate' => true,
    ]"
/>