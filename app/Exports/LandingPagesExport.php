<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class LandingPagesExport implements FromCollection, WithHeadings, WithMapping
{
    protected $pages;

    public function __construct($pages)
    {
        $this->pages = $pages;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->pages;
    }

    public function headings(): array
    {
        return [
            'Page Code',
            'Page Title',
            'Page URL',
            'Language',
            'Is Active',
            'Campaign',
            'Created At',
        ];
    }

    public function map($page): array
    {
        return [
            $page->page_code,
            $page->page_title,
            $page->page_url,
            $page->language,
            $page->is_active ? 'Yes' : 'No',
            $page->campaign ? $page->campaign->campaign_name : 'N/A',
            $page->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
