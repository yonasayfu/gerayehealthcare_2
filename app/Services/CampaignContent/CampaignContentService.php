<?php

namespace App\Services\CampaignContent;

use App\Models\CampaignContent;
use App\Services\Base\BaseService;
use Illuminate\Http\Request;

class CampaignContentService extends BaseService
{
    public function __construct(CampaignContent $campaignContent)
    {
        parent::__construct($campaignContent);
    }

    protected function applySearch($query, $search)
    {
        $query->where('title', 'ilike', "%{$search}%")
            ->orWhere('description', 'ilike', "%{$search}%");
    }

    public function getAll(Request $request, array $with = [])
    {
        // Always eager-load campaign and platform; merge with any additional requested relations
        $with = array_unique(array_merge($with, ['campaign', 'platform']));
        $query = $this->model->query()->with($with);

        if ($request->filled('search')) {
            // Group search conditions to not break other where clauses
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'ilike', "%{$search}%")
                    ->orWhere('description', 'ilike', "%{$search}%");
            });
        }

        if ($request->filled('campaign_id')) {
            $query->where('campaign_id', $request->input('campaign_id'));
        }
        if ($request->filled('platform_id')) {
            $query->where('platform_id', $request->input('platform_id'));
        }
        if ($request->filled('content_type')) {
            $query->where('content_type', $request->input('content_type'));
        }
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }
        if ($request->filled('scheduled_post_date_start')) {
            $query->where('scheduled_post_date', '>=', $request->input('scheduled_post_date_start'));
        }
        if ($request->filled('scheduled_post_date_end')) {
            $query->where('scheduled_post_date', '<=', $request->input('scheduled_post_date_end'));
        }

        if ($request->has('sort')) {
            $direction = $request->input('direction', 'asc');
            $query->orderBy($request->input('sort'), $direction);
        }

        // Preserve current query string so pagination maintains filters/sort
        return $query->paginate($request->input('per_page', 5))->withQueryString();
    }
}
