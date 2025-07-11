<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\TaskDelegation;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TaskDelegationController extends Controller
{
    public function index(Request $request)
    {
        $staffId = auth()->user()->staff->id;

        $tasks = TaskDelegation::with('assignee')
            ->where('assigned_to', $staffId)
            ->orderBy('due_date','asc')
            ->paginate($request->input('per_page', 15))
            ->withQueryString();

        return Inertia::render('Staff/TaskDelegations/Index', [
            'taskDelegations' => $tasks,
        ]);
    }
}
