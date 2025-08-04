<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMarketingTaskRequest;
use App\Http\Requests\UpdateMarketingTaskRequest;
use App\Models\MarketingTask;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class MarketingTaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): \Inertia\Response
    {
        $query = MarketingTask::query();

        // Filter by assigned staff member or doctor
        if (Auth::check()) {
            $user = Auth::user();
            $staffMember = \App\Models\Staff::where('user_id', $user->id)->first();
            if ($staffMember) {
                $query->where(function ($q) use ($staffMember) {
                    $q->where('assigned_to_staff_id', $staffMember->id)
                      ->orWhere('doctor_id', $staffMember->id);
                });
            }
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('title', 'ilike', "%{$search}%")
                  ->orWhere('description', 'ilike', "%{$search}%")
                  ->orWhere('task_code', 'ilike', "%{$search}%");
        }

        // Filtering
        if ($request->filled('campaign_id')) {
            $query->where('campaign_id', $request->input('campaign_id'));
        }
        if ($request->filled('task_type')) {
            $query->where('task_type', $request->input('task_type'));
        }
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }
        if ($request->filled('scheduled_at_start')) {
            $query->where('scheduled_at', '>=', $request->input('scheduled_at_start'));
        }
        if ($request->filled('scheduled_at_end')) {
            $query->where('scheduled_at', '<=', $request->input('scheduled_at_end'));
        }

        // Sorting
        if ($request->filled('sort') && !empty($request->input('sort'))) {
            $query->orderBy($request->input('sort'), $request->input('direction', 'asc'));
        } else {
            $query->orderBy('scheduled_at', 'asc');
        }

        $marketingTasks = $query->with(['campaign', 'assignedToStaff', 'relatedContent', 'doctor'])
                                 ->paginate($request->input('per_page', 5))
                                 ->withQueryString();

        return Inertia::render('Staff/MarketingTasks/Index', [
            'marketingTasks' => $marketingTasks,
            'filters' => $request->only(['search', 'sort', 'direction', 'per_page', 'campaign_id', 'task_type', 'status', 'scheduled_at_start', 'scheduled_at_end']),
        ]);
    }

    
}
