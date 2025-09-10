<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\PersonalTask;
use App\Models\TaskDelegation;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DailyTaskTrackingController extends Controller
{
    /**
     * Display the daily task tracking dashboard
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $staff = $user->staff;
        
        if (!$staff) {
            return redirect()->route('dashboard')->with('error', 'You do not have a staff profile.');
        }
        
        $date = $request->input('date', now()->toDateString());
        $selectedDate = \Carbon\Carbon::parse($date);
        
        // Get personal tasks for the selected date
        $personalTasks = PersonalTask::where('user_id', $user->id)
            ->whereDate('task_date', $selectedDate)
            ->with('subtasks')
            ->orderBy('priority_level', 'desc')
            ->orderBy('created_at')
            ->get();
            
        // Get delegated tasks for the selected date
        $delegatedTasks = TaskDelegation::where('assigned_to', $staff->id)
            ->whereDate('task_date', $selectedDate)
            ->with('assignee')
            ->orderBy('priority_level', 'desc')
            ->orderBy('created_at')
            ->get();
            
        // Calculate daily statistics
        $stats = $this->calculateDailyStats($user->id, $staff->id, $selectedDate);
        
        return Inertia::render('Staff/DailyTaskTracking/Index', [
            'personalTasks' => $personalTasks,
            'delegatedTasks' => $delegatedTasks,
            'date' => $selectedDate->toDateString(),
            'stats' => $stats,
        ]);
    }
    
    /**
     * Calculate daily statistics for the dashboard
     */
    private function calculateDailyStats($userId, $staffId, $date)
    {
        // Personal tasks stats
        $personalTasks = PersonalTask::where('user_id', $userId)
            ->whereDate('task_date', $date)
            ->get();
            
        $personalCompleted = $personalTasks->where('is_completed', true)->count();
        $personalTotal = $personalTasks->count();
        
        // Delegated tasks stats
        $delegatedTasks = TaskDelegation::where('assigned_to', $staffId)
            ->whereDate('task_date', $date)
            ->get();
            
        $delegatedCompleted = $delegatedTasks->where('status', 'Completed')->count();
        $delegatedInProgress = $delegatedTasks->where('status', 'In Progress')->count();
        $delegatedTotal = $delegatedTasks->count();
        
        // Calculate time spent (if start and end times are recorded)
        $totalMinutes = 0;
        foreach ($personalTasks->merge($delegatedTasks) as $task) {
            if ($task->start_time && $task->end_time) {
                $start = \Carbon\Carbon::parse($task->start_time);
                $end = \Carbon\Carbon::parse($task->end_time);
                $totalMinutes += $end->diffInMinutes($start);
            }
        }
        
        return [
            'personalTasksCompleted' => $personalCompleted,
            'personalTasksTotal' => $personalTotal,
            'delegatedTasksCompleted' => $delegatedCompleted,
            'delegatedTasksInProgress' => $delegatedInProgress,
            'delegatedTasksTotal' => $delegatedTotal,
            'totalTimeMinutes' => $totalMinutes,
            'completionRate' => $delegatedTotal > 0 ? round(($delegatedCompleted / $delegatedTotal) * 100, 1) : 0,
        ];
    }
    
    /**
     * Update a task with daily tracking information
     */
    public function updateTask(Request $request)
    {
        $validated = $request->validate([
            'task_type' => 'required|in:personal,delegated',
            'task_id' => 'required|integer',
            'task_date' => 'nullable|date',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i',
            'estimated_duration_minutes' => 'nullable|integer|min:1',
            'daily_notes' => 'nullable|string|max:1000',
            'task_category' => 'nullable|string|max:100',
            'priority_level' => 'nullable|integer|min:1|max:5',
            'is_billable' => 'nullable|boolean',
            'progress_status' => 'nullable|string|in:not_started,in_progress,completed,blocked',
        ]);
        
        $user = Auth::user();
        $staff = $user->staff;
        
        if ($validated['task_type'] === 'personal') {
            $task = PersonalTask::where('user_id', $user->id)->findOrFail($validated['task_id']);
        } else {
            if (!$staff) {
                return response()->json(['error' => 'You do not have a staff profile.'], 403);
            }
            $task = TaskDelegation::where('assigned_to', $staff->id)->findOrFail($validated['task_id']);
        }
        
        // Update task with daily tracking information
        $task->update([
            'task_date' => $validated['task_date'] ?? $task->task_date,
            'start_time' => $validated['start_time'] ?? $task->start_time,
            'end_time' => $validated['end_time'] ?? $task->end_time,
            'estimated_duration_minutes' => $validated['estimated_duration_minutes'] ?? $task->estimated_duration_minutes,
            'daily_notes' => $validated['daily_notes'] ?? $task->daily_notes,
            'task_category' => $validated['task_category'] ?? $task->task_category,
            'priority_level' => $validated['priority_level'] ?? $task->priority_level,
            'is_billable' => $validated['is_billable'] ?? $task->is_billable,
            'progress_status' => $validated['progress_status'] ?? $task->progress_status,
        ]);
        
        return response()->json(['message' => 'Task updated successfully.']);
    }
    
    /**
     * Get KPI dashboard data for supervisors
     */
    public function kpiDashboard(Request $request)
    {
        $user = Auth::user();
        
        // Check if user has permission to view KPI dashboard
        if (!$user->hasRole(['Super Admin', 'Admin', 'CEO', 'COO'])) {
            return redirect()->route('dashboard')->with('error', 'You do not have permission to view the KPI dashboard.');
        }
        
        $dateRange = $request->input('range', 'week'); // week, month, quarter
        $department = $request->input('department');
        
        // Get date range
        $startDate = now();
        $endDate = now();
        
        switch ($dateRange) {
            case 'week':
                $startDate = now()->startOfWeek();
                $endDate = now()->endOfWeek();
                break;
            case 'month':
                $startDate = now()->startOfMonth();
                $endDate = now()->endOfMonth();
                break;
            case 'quarter':
                $startDate = now()->startOfQuarter();
                $endDate = now()->endOfQuarter();
                break;
        }
        
        // Get staff members (filter by department if specified)
        $staffQuery = Staff::with('user');
        if ($department) {
            $staffQuery->where('department', $department);
        }
        $staffMembers = $staffQuery->get();
        
        // Calculate KPIs for each staff member
        $kpiData = [];
        foreach ($staffMembers as $staff) {
            $kpiData[] = $this->calculateStaffKPIs($staff, $startDate, $endDate);
        }
        
        // Get departments for filter
        $departments = Staff::select('department')->distinct()->pluck('department');
        
        return Inertia::render('Staff/DailyTaskTracking/KPIDashboard', [
            'kpiData' => $kpiData,
            'dateRange' => $dateRange,
            'department' => $department,
            'departments' => $departments,
            'startDate' => $startDate->toDateString(),
            'endDate' => $endDate->toDateString(),
        ]);
    }
    
    /**
     * Calculate KPIs for a specific staff member
     */
    private function calculateStaffKPIs($staff, $startDate, $endDate)
    {
        // Get tasks for the staff member in the date range
        $delegatedTasks = TaskDelegation::where('assigned_to', $staff->id)
            ->whereBetween('task_date', [$startDate, $endDate])
            ->get();
            
        $totalTasks = $delegatedTasks->count();
        $completedTasks = $delegatedTasks->where('status', 'Completed')->count();
        $inProgressTasks = $delegatedTasks->where('status', 'In Progress')->count();
        
        // Calculate completion rate
        $completionRate = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100, 1) : 0;
        
        // Calculate average task duration
        $totalDuration = 0;
        $tasksWithDuration = 0;
        foreach ($delegatedTasks as $task) {
            if ($task->start_time && $task->end_time) {
                $start = \Carbon\Carbon::parse($task->start_time);
                $end = \Carbon\Carbon::parse($task->end_time);
                $totalDuration += $end->diffInMinutes($start);
                $tasksWithDuration++;
            }
        }
        $avgDuration = $tasksWithDuration > 0 ? round($totalDuration / $tasksWithDuration, 1) : 0;
        
        // Calculate on-time rate (tasks completed by due date)
        $onTimeTasks = 0;
        $completedWithDueDate = 0;
        foreach ($delegatedTasks->where('status', 'Completed') as $task) {
            if ($task->due_date) {
                $completedWithDueDate++;
                if (\Carbon\Carbon::parse($task->task_date)->lte(\Carbon\Carbon::parse($task->due_date))) {
                    $onTimeTasks++;
                }
            }
        }
        $onTimeRate = $completedWithDueDate > 0 ? round(($onTimeTasks / $completedWithDueDate) * 100, 1) : 0;
        
        return [
            'staff' => [
                'id' => $staff->id,
                'name' => $staff->first_name . ' ' . $staff->last_name,
                'position' => $staff->position,
                'department' => $staff->department,
            ],
            'totalTasks' => $totalTasks,
            'completedTasks' => $completedTasks,
            'inProgressTasks' => $inProgressTasks,
            'completionRate' => $completionRate,
            'avgDurationMinutes' => $avgDuration,
            'onTimeRate' => $onTimeRate,
        ];
    }
}