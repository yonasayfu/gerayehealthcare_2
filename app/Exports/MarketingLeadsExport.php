<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class MarketingLeadsExport implements FromCollection, WithHeadings, WithMapping
{
    protected $leads;

    public function __construct($leads)
    {
        $this->leads = $leads;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->leads;
    }

    public function headings(): array
    {
        return [
            'Lead Code',
            'First Name',
            'Last Name',
            'Email',
            'Phone',
            'Status',
            'Source Campaign',
            'Assigned Staff',
            'Created At',
        ];
    }

    public function map($lead): array
    {
        return [
            $lead->lead_code,
            $lead->first_name,
            $lead->last_name,
            $lead->email,
            $lead->phone,
            $lead->status,
            $lead->sourceCampaign ? $lead->sourceCampaign->campaign_name : 'N/A',
            $lead->assignedStaff ? $lead->assignedStaff->full_name : 'N/A',
            $lead->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
