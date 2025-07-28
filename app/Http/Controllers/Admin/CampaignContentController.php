<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCampaignContentRequest;
use App\Http\Requests\UpdateCampaignContentRequest;
use App\Models\CampaignContent;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CampaignContentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): \Inertia\Response
    {
        $query = CampaignContent::query();

        // Search
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('title', 'ilike', "%{$search}%")
                  ->orWhere('description', 'ilike', "%{$search}%");
        }

        // Filtering
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

        // Sorting
        if ($request->filled('sort') && !empty($request->input('sort'))) {
            $query->orderBy($request->input('sort'), $request->input('direction', 'asc'));
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $campaignContents = $query->with(['campaign', 'platform'])
                                   ->paginate($request->input('per_page', 10))
                                   ->withQueryString();

        return Inertia::render('Admin/CampaignContents/Index', [
            'campaignContents' => $campaignContents,
            'filters' => $request->only(['search', 'sort', 'direction', 'per_page', 'campaign_id', 'platform_id', 'content_type', 'status', 'scheduled_post_date_start', 'scheduled_post_date_end']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Admin/CampaignContents/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCampaignContentRequest $request)
    {
        CampaignContent::create($request->validated());

        return redirect()->route('admin.campaign-contents.index')->with('success', 'Campaign Content created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(CampaignContent $campaignContent)
    {
        $campaignContent->load(['campaign', 'platform']);

        return Inertia::render('Admin/CampaignContents/Show', [
            'campaignContent' => $campaignContent,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CampaignContent $campaignContent)
    {
        $campaignContent->load(['campaign', 'platform']);

        return Inertia::render('Admin/CampaignContents/Edit', [
            'campaignContent' => $campaignContent,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCampaignContentRequest $request, CampaignContent $campaignContent)
    {
        $campaignContent->update($request->validated());

        return redirect()->route('admin.campaign-contents.index')->with('success', 'Campaign Content updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CampaignContent $campaignContent)
    {
        $campaignContent->delete();

        return back()->with('success', 'Campaign Content deleted successfully.');
    }
}
