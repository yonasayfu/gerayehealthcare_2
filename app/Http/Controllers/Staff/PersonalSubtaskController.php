<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\PersonalTask;
use App\Models\PersonalTaskSubtask;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PersonalSubtaskController extends Controller
{
    protected function authorizeTask(PersonalTask $task): void
    {
        abort_unless($task->user_id === Auth::id(), 403);
    }

    public function store(Request $request, PersonalTask $my_todo)
    {
        $this->authorizeTask($my_todo);
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
        ]);
        $position = ($my_todo->subtasks()->max('position') ?? 0) + 1;
        $my_todo->subtasks()->create([
            'title' => $validated['title'],
            'position' => $position,
        ]);
        return back();
    }

    public function update(Request $request, PersonalTask $my_todo, PersonalTaskSubtask $subtask)
    {
        $this->authorizeTask($my_todo);
        abort_unless($subtask->personal_task_id === $my_todo->id, 404);
        $validated = $request->validate([
            'title' => ['sometimes', 'string', 'max:255'],
            'is_completed' => ['sometimes', 'boolean'],
        ]);
        $subtask->update($validated);
        return back();
    }

    public function destroy(PersonalTask $my_todo, PersonalTaskSubtask $subtask)
    {
        $this->authorizeTask($my_todo);
        abort_unless($subtask->personal_task_id === $my_todo->id, 404);
        $subtask->delete();
        return back();
    }
}

