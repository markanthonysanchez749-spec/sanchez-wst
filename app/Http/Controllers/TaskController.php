<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TaskController extends Controller
{
    public function index(): View
    {
        $tasks = Task::orderByRaw("CASE WHEN status = 'In Progress' THEN 0 WHEN status = 'Pending' THEN 1 ELSE 2 END")
            ->orderBy('due_date')
            ->latest()
            ->get();

        $pendingCount = Task::where('status', 'Pending')->count();
        $inProgressCount = Task::where('status', 'In Progress')->count();
        $completedCount = Task::where('status', 'Completed')->count();
        $overdueCount = Task::where('status', '!=', 'Completed')
            ->whereDate('due_date', '<', now()->toDateString())
            ->count();

        return view('tasks.index', [
            'tasks' => $tasks,
            'pendingCount' => $pendingCount,
            'inProgressCount' => $inProgressCount,
            'completedCount' => $completedCount,
            'overdueCount' => $overdueCount,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'task_name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'in:Pending,In Progress,Completed'],
            'due_date' => ['nullable', 'date'],
        ]);

        Task::create($validated);

        return redirect()->route('tasks.index');
    }

    public function edit(Task $task): View
    {
        $tasks = Task::orderByRaw("CASE WHEN status = 'In Progress' THEN 0 WHEN status = 'Pending' THEN 1 ELSE 2 END")
            ->orderBy('due_date')
            ->latest()
            ->get();

        return view('tasks.index', [
            'tasks' => $tasks,
            'editingTask' => $task,
            'pendingCount' => Task::where('status', 'Pending')->count(),
            'inProgressCount' => Task::where('status', 'In Progress')->count(),
            'completedCount' => Task::where('status', 'Completed')->count(),
            'overdueCount' => Task::where('status', '!=', 'Completed')
                ->whereDate('due_date', '<', now()->toDateString())
                ->count(),
        ]);
    }

    public function update(Request $request, Task $task): RedirectResponse
    {
        $validated = $request->validate([
            'task_name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'in:Pending,In Progress,Completed'],
            'due_date' => ['nullable', 'date'],
        ]);

        $task->update($validated);

        return redirect()->route('tasks.index');
    }

    public function updateStatus(Request $request, Task $task): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:Pending,In Progress,Completed'],
        ]);

        $task->update($validated);

        return redirect()->route('tasks.index');
    }

    public function destroy(Task $task): RedirectResponse
    {
        $task->delete();

        return redirect()->route('tasks.index');
    }
}
