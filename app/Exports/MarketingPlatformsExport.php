<?php

namespace App\Exports;

use App\Models\MarketingPlatform;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class MarketingPlatformsExport implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping
{
    public function collection()
    {
        return MarketingPlatform::all();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'API Endpoint',
            'API Credentials',
            'Is Active',
            'Created At',
            'Updated At',
        ];
    }

    public function map($platform): array
    {
        return [
            $platform->id,
            $platform->name,
            $platform->api_endpoint,
            $platform->api_credentials,
            $platform->is_active ? 'Yes' : 'No',
            $platform->created_at,
            $platform->updated_at,
        ];
    }
}
