<?php

namespace App\Exports;

use App\Models\CampaignContent;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class CampaignContentsExport implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping
{
    public function collection()
    {
        return CampaignContent::with(['campaign', 'platform'])->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Campaign ID',
            'Platform ID',
            'Content Type',
            'Title',
            'Description',
            'Media URL',
            'Scheduled Post Date',
            'Actual Post Date',
            'Status',
            'Engagement Metrics',
            'Created At',
            'Updated At',
        ];
    }

    public function map($content): array
    {
        return [
            $content->id,
            $content->campaign->campaign_name ?? '-',
            $content->platform->name ?? '-',
            $content->content_type,
            $content->title,
            $content->description,
            $content->media_url,
            $content->scheduled_post_date,
            $content->actual_post_date,
            $content->status,
            json_encode($content->engagement_metrics),
            $content->created_at,
            $content->updated_at,
        ];
    }
}
