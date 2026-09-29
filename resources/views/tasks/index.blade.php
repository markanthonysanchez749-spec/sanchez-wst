<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Task Manager Dashboard</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-[#f6f8fb] text-slate-900 antialiased">
        <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
            <header class="mb-8 flex flex-col gap-5 border-b border-slate-200 pb-5 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-teal-600 text-white shadow-sm shadow-teal-200">
                        <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path d="M5 5.5A2.5 2.5 0 0 1 7.5 3h9A2.5 2.5 0 0 1 19 5.5v13a2.5 2.5 0 0 1-2.5 2.5h-9A2.5 2.5 0 0 1 5 18.5v-13Z" />
                            <path d="m8 12 2.2 2.2L16 8.5" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-slate-950">Task manager</p>
                        <p class="text-xs text-slate-500">Personal workspace</p>
                    </div>
                </div>

                <div>
                    <p class="text-sm text-slate-500">{{ now()->format('D, M j, Y') }}</p>
                </div>
            </header>

            <div class="mb-8 grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
                <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm shadow-slate-200/50">
                    <div class="flex items-center justify-between">
                        <p class="text-sm text-slate-500">Pending</p>
                        <span class="h-2 w-2 rounded-full bg-amber-400"></span>
                    </div>
                    <p class="mt-3 text-3xl font-semibold tracking-tight text-slate-950">{{ $pendingCount }}</p>
                </div>
                <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm shadow-slate-200/50">
                    <div class="flex items-center justify-between">
                        <p class="text-sm text-slate-500">In progress</p>
                        <span class="h-2 w-2 rounded-full bg-sky-500"></span>
                    </div>
                    <p class="mt-3 text-3xl font-semibold tracking-tight text-slate-950">{{ $inProgressCount }}</p>
                </div>
                <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm shadow-slate-200/50">
                    <div class="flex items-center justify-between">
                        <p class="text-sm text-slate-500">Completed</p>
                        <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                    </div>
                    <p class="mt-3 text-3xl font-semibold tracking-tight text-slate-950">{{ $completedCount }}</p>
                </div>
                <div class="rounded-2xl border border-rose-200 bg-rose-50 p-4 shadow-sm shadow-rose-100/70">
                    <div class="flex items-center justify-between">
                        <p class="text-sm text-rose-700">Overdue</p>
                        <span class="h-2 w-2 rounded-full bg-rose-500"></span>
                    </div>
                    <p class="mt-3 text-3xl font-semibold tracking-tight text-rose-950">{{ $overdueCount }}</p>
                </div>
            </div>

            <main class="grid gap-8 lg:grid-cols-[1.1fr_0.9fr]">
                <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm shadow-slate-200/60 sm:p-6">
                    <div class="mb-5 flex items-end justify-between border-b border-slate-100 pb-4">
                        <div>
                            <h2 class="text-lg font-semibold text-slate-950">Task list</h2>
                            <p class="text-sm text-slate-500">{{ $tasks->count() }} {{ $tasks->count() === 1 ? 'task' : 'tasks' }}</p>
                        </div>
                    </div>

                    <div class="space-y-3">
                        @forelse($tasks as $task)
                            @php($isOverdue = $task->due_date && $task->status !== 'Completed' && $task->due_date->isBefore(today()))
                            <article class="flex flex-col gap-4 rounded-xl border {{ $isOverdue ? 'border-rose-200 bg-rose-50/70' : 'border-slate-200 bg-slate-50/60' }} p-4 transition hover:border-teal-300 hover:bg-white hover:shadow-md hover:shadow-slate-200/60 sm:flex-row sm:items-start">
                                <form action="{{ route('tasks.status', $task) }}" method="POST" class="shrink-0">
                                    @csrf
                                    @method('PATCH')
                                    <label for="task-status-{{ $task->id }}" class="sr-only">Status for {{ $task->task_name }}</label>
                                    <select id="task-status-{{ $task->id }}" name="status" onchange="this.form.submit()" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm font-semibold text-slate-700 shadow-sm outline-none transition focus:border-teal-500 focus:ring-2 focus:ring-teal-100 sm:w-36">
                                        <option value="Pending" {{ $task->status === 'Pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="In Progress" {{ $task->status === 'In Progress' ? 'selected' : '' }}>In Progress</option>
                                        <option value="Completed" {{ $task->status === 'Completed' ? 'selected' : '' }}>Completed</option>
                                    </select>
                                </form>

                                <div class="min-w-0 flex-1">
                                    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                                        <div>
                                            <h3 class="text-lg font-medium {{ $task->status === 'Completed' ? 'text-slate-400 line-through' : 'text-slate-950' }}">{{ $task->task_name }}</h3>
                                            @if($task->description)
                                                <p class="mt-1 text-sm text-slate-500">{{ $task->description }}</p>
                                            @endif
                                        </div>
                                        <div class="flex items-center gap-2">
                                            @if($isOverdue)
                                                <span class="inline-flex items-center gap-1 rounded-full border border-rose-200 bg-rose-50 px-2 py-1 text-[10px] font-semibold uppercase tracking-[0.2em] text-rose-700">
                                                    Overdue
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="mt-3 flex flex-col gap-2 text-xs text-slate-500 sm:flex-row sm:items-center sm:justify-between">
                                        <span>
                                            @if($task->due_date)
                                                <span class="{{ $isOverdue ? 'font-medium text-rose-700' : '' }}">
                                                    {{ $isOverdue ? 'Overdue' : 'Due' }} {{ $task->due_date->format('M d, Y') }}
                                                </span>
                                            @else
                                                No due date
                                            @endif
                                        </span>
                                        <div class="flex items-center gap-2">
                                            <a href="{{ route('tasks.edit', $task) }}" class="rounded-full border border-slate-200 bg-slate-50 px-3 py-1.5 text-slate-700 transition hover:border-teal-400 hover:text-teal-700">Edit</a>
                                            <form action="{{ route('tasks.destroy', $task) }}" method="POST" onsubmit="return confirm('Delete this task?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="rounded-full border border-rose-200 bg-rose-50 px-3 py-1.5 text-rose-700 transition hover:bg-rose-100">Delete</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </article>
                        @empty
                            <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-8 text-center">
                                <p class="text-lg font-medium text-slate-950">No tasks yet</p>
                                <p class="mt-2 text-sm text-slate-500">Add your first task to get started.</p>
                            </div>
                        @endforelse
                    </div>
                </section>

                <aside id="new-task" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm shadow-slate-200/60 sm:p-6">
                    <div class="mb-5">
                        <h2 class="text-lg font-semibold text-slate-950">{{ isset($editingTask) ? 'Edit task' : 'Add a task' }}</h2>
                        <p class="text-sm text-slate-500">{{ isset($editingTask) ? 'Change the details below.' : 'Add something you need to remember.' }}</p>
                    </div>

                    <form action="{{ isset($editingTask) ? route('tasks.update', $editingTask) : route('tasks.store') }}" method="POST" class="space-y-4">
                        @csrf
                        @if(isset($editingTask))
                            @method('PUT')
                        @endif

                        <div>
                            <label for="task_name" class="mb-1.5 block text-sm text-slate-700">Task name</label>
                            <input id="task_name" name="task_name" value="{{ old('task_name', $editingTask->task_name ?? '') }}" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm text-slate-950 placeholder:text-slate-400 focus:border-teal-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-100" placeholder="Prepare assignment" required />
                            @error('task_name')
                                <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="description" class="mb-1.5 block text-sm text-slate-700">Description</label>
                            <textarea id="description" name="description" rows="4" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm text-slate-950 placeholder:text-slate-400 focus:border-teal-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-100" placeholder="Add task details...">{{ old('description', $editingTask->description ?? '') }}</textarea>
                            @error('description')
                                <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label for="status" class="mb-1.5 block text-sm text-slate-700">Status</label>
                                <select id="status" name="status" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm text-slate-950 focus:border-teal-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-100">
                                    <option value="Pending" {{ old('status', $editingTask->status ?? 'Pending') === 'Pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="In Progress" {{ old('status', $editingTask->status ?? 'Pending') === 'In Progress' ? 'selected' : '' }}>In Progress</option>
                                    <option value="Completed" {{ old('status', $editingTask->status ?? 'Pending') === 'Completed' ? 'selected' : '' }}>Completed</option>
                                </select>
                            </div>

                            <div>
                                <label for="due_date" class="mb-1.5 block text-sm text-slate-700">Due date</label>
                                <input type="date" id="due_date" name="due_date" value="{{ old('due_date', isset($editingTask) && $editingTask->due_date ? $editingTask->due_date->format('Y-m-d') : '') }}" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm text-slate-950 focus:border-teal-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-100" />
                                @error('due_date')
                                    <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="flex items-center gap-3 pt-2">
                            <button type="submit" class="rounded-full bg-teal-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-teal-700">
                                {{ isset($editingTask) ? 'Update task' : 'Save task' }}
                            </button>
                            @if(isset($editingTask))
                                <a href="{{ route('tasks.index') }}" class="rounded-full border border-slate-200 px-4 py-2.5 text-sm text-slate-700 transition hover:border-slate-300">Cancel</a>
                            @endif
                        </div>
                    </form>
                </aside>
            </main>
        </div>
    </body>
</html>
