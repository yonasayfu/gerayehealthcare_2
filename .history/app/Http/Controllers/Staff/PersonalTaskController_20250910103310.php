<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\PersonalTask;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class PersonalTaskController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();
        $filter = $request->input('filter', 'all'); // all|today|upcoming|important|completed|myday

        $query = PersonalTask::where('user_id', $userId);
        $today = now()->startOfDay();

        switch ($filter) {
            case 'today':
                $query->whereDate('due_date', $today);
                break;
            case 'upcoming':
                $query->whereDate('due_date', '>', $today);
                break;
            case 'important':
                $query->where('is_important', true)->where('is_completed', false);
                break;
            case 'completed':
                $query->where('is_completed', true);
                break;
            case 'myday':
                $query->whereDate('my_day_for', $today);
                break;
            default:
                // all: no extra constraints
                break;
        }

        $tasks = $query->with('subtasks')
            ->orderByRaw("is_completed asc, is_important desc, coalesce(due_date, '9999-12-31') asc, created_at desc")
            ->paginate($request->input('per_page', 20))
            ->withQueryString();

        return Inertia::render('Staff/MyToDo/Index', [
            'tasks' => $tasks,
            'filters' => [
                'filter' => $filter,
                'per_page' => (int) $request->input('per_page', 20),
            ],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
            'due_date' => ['nullable', 'date'],
            'reminder_at' => ['nullable', 'date'],
            'is_important' => ['nullable', 'boolean'],
            'add_to_my_day' => ['nullable', 'boolean'],
            'recurrence_type' => ['nullable', 'in:none,daily,weekly'],
        ]);

        $myDayFor = !empty($validated['add_to_my_day']) ? now()->toDateString() : null;
        $recurrenceType = $validated['recurrence_type'] ?? 'none';
        PersonalTask::create([
            'user_id' => Auth::id(),
            'title' => $validated['title'],
            'notes' => $validated['notes'] ?? null,
            'due_date' => $validated['due_date'] ?? null,
            'reminder_at' => $validated['reminder_at'] ?? null,
            'is_important' => (bool) ($validated['is_important'] ?? false),
            'my_day_for' => $myDayFor,
            'recurrence_type' => $recurrenceType,
            'recurrence_weekdays' => $recurrenceType === 'weekly' ? [now()->dayOfWeek] : null,
        ]);

        return back()->with('banner', 'To-Do created.')->with('bannerStyle', 'success');
    }

    public function update(Request $request, PersonalTask $my_todo)
    {
        abort_unless($my_todo->user_id === Auth::id(), 403);
        $validated = $request->validate([
            'title' => ['sometimes', 'string', 'max:255'],
            'notes' => ['sometimes', 'nullable', 'string'],
            'due_date' => ['sometimes', 'nullable', 'date'],
            'reminder_at' => ['sometimes', 'nullable', 'date'],
            'is_important' => ['sometimes', 'boolean'],
            'is_completed' => ['sometimes', 'boolean'],
            'add_to_my_day' => ['sometimes', 'boolean'],
            'recurrence_type' => ['sometimes', 'in:none,daily,weekly'],
            'recurrence_weekdays' => ['sometimes', 'array'],
        ]);
        if (array_key_exists('add_to_my_day', $validated)) {
            $validated['my_day_for'] = $validated['add_to_my_day'] ? now()->toDateString() : null;
            unset($validated['add_to_my_day']);
        }
        $my_todo->update($validated);
        return back();
    }

    public function destroy(PersonalTask $my_todo)
    {
        abort_unless($my_todo->user_id === Auth::id(), 403);
        $my_todo->delete();
        return back();
    }
}
