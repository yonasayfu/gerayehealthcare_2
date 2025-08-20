<?php

namespace App\Services;

use App\Http\Config\ExportConfig;
use App\Http\Traits\ExportableTrait;
use App\Models\MarketingPlatform;
use Illuminate\Http\Request;

class MarketingPlatformService extends BaseService
{
    use ExportableTrait;

    public function __construct(MarketingPlatform $marketingPlatform)
    {
        parent::__construct($marketingPlatform);
    }

    protected function applySearch($query, $search)
    {
        return $query->where('name', 'ilike', "%{$search}%");
    }

    public function getAll(Request $request, array $with = [])
    {
        $query = $this->model->with($with);

        if ($request->has('search')) {
            $this->applySearch($query, $request->input('search'));
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->input('is_active'));
        }

        if ($request->has('sort')) {
            $direction = $request->input('direction', 'asc');
            $query->orderBy($request->input('sort'), $direction);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        return $query->paginate($request->input('per_page', 10));
    }

    public function toggleStatus(MarketingPlatform $marketingPlatform): MarketingPlatform
    {
        $marketingPlatform->is_active = ! $marketingPlatform->is_active;
        $marketingPlatform->save();

        return $marketingPlatform;
    }

    public function printCurrent(Request $request)
    {
        return $this->handlePrintCurrent($request, MarketingPlatform::class, ExportConfig::getMarketingPlatformConfig());
    }
}
