# Personal Task Manager: Project Explanation

## 1. Project Overview

This project is a Personal Task Manager built with Laravel. It allows a user to create, view, edit, delete, and update tasks.

Each task contains:

- `task_name`
- `description`
- `status`
- `due_date`

Supported statuses are:

- Pending
- In Progress
- Completed

The system also identifies overdue tasks when a task has a due date in the past and is not yet completed.

## 2. Technologies Used

- Laravel 12: PHP web application framework
- PHP 8.2: Backend programming language
- Blade: Laravel server-side templating engine
- Eloquent ORM: Database interaction
- SQLite: Local database
- Tailwind CSS: Interface styling
- Vite: Frontend asset build tool
- PHPUnit and Laravel tests: Automated testing

## 3. Application Structure

The project follows Laravel's MVC structure:

```text
User
  -> Route
  -> Controller
  -> Model
  -> Database
  -> Blade View
```

### Routes

Routes receive browser requests and send them to the correct controller method. They are defined in `routes/web.php`.

### Controller

The controller contains the application logic. It validates input, creates records, updates records, calculates statistics, and deletes records.

### Model

The `Task` model represents a task record and defines which fields can be safely assigned.

### Database

SQLite stores task records. Migrations define the database structure so it can be recreated consistently.

### Blade View

The Blade view displays the dashboard, task list, statistics, forms, status dropdowns, edit links, and delete buttons.

## 4. Routes

The application routes are defined in `routes/web.php`:

```php
Route::redirect('/', '/tasks');

Route::resource('tasks', TaskController::class)->except(['show']);

Route::patch('/tasks/{task}/status', [TaskController::class, 'updateStatus'])
    ->name('tasks.status');
```

The resource route provides the main CRUD endpoints:

| Method | URL | Purpose |
|---|---|---|
| GET | `/tasks` | Display all tasks |
| POST | `/tasks` | Create a task |
| GET | `/tasks/{task}/edit` | Display the edit form |
| PUT/PATCH | `/tasks/{task}` | Update a task |
| DELETE | `/tasks/{task}` | Delete a task |
| PATCH | `/tasks/{task}/status` | Update only the task status |

The `show` route is excluded because the application uses one dashboard instead of a separate task details page.

## 5. Controller Logic

The main controller is `app/Http/Controllers/TaskController.php`.

### `index()`

The `index()` method:

1. Retrieves all tasks.
2. Sorts tasks by status and due date.
3. Counts Pending tasks.
4. Counts In Progress tasks.
5. Counts Completed tasks.
6. Counts overdue tasks.
7. Sends the results to the dashboard view.

Active tasks appear before completed tasks.

### `store()`

The `store()` method validates and saves new tasks:

```php
$validated = $request->validate([
    'task_name' => ['required', 'string', 'max:255'],
    'description' => ['nullable', 'string'],
    'status' => ['required', 'in:Pending,In Progress,Completed'],
    'due_date' => ['nullable', 'date'],
]);

Task::create($validated);
```

Validation prevents empty task names, invalid statuses, and invalid dates.

### `edit()`

The `edit()` method loads the selected task and displays it in the same dashboard form. This keeps the interface simple instead of creating a separate edit page.

### `update()`

The `update()` method validates the edited values and updates the selected task using Eloquent.

### `updateStatus()`

The status dropdown uses a dedicated PATCH request:

```php
$validated = $request->validate([
    'status' => ['required', 'in:Pending,In Progress,Completed'],
]);

$task->update($validated);
```

This route updates only the status and does not require submitting the entire task form.

The Blade form uses Laravel method spoofing:

```blade
<form method="POST">
    @csrf
    @method('PATCH')
</form>
```

HTML forms support GET and POST directly, so `@method('PATCH')` tells Laravel to process the request as PATCH.

### `destroy()`

The `destroy()` method removes a task using:

```php
$task->delete();
```

## 6. Model

The model is located at `app/Models/Task.php`.

```php
protected $fillable = [
    'task_name',
    'description',
    'status',
    'due_date',
];
```

The `$fillable` property protects against mass assignment by allowing only the required task fields to be assigned through user input.

The due date is cast as a date:

```php
protected $casts = [
    'due_date' => 'date',
];
```

This allows the view to format the due date cleanly.

## 7. Database

The final tasks table contains:

```text
id
created_at
updated_at
description
due_date
task_name
status
```

The original development process included repair migrations because the database initially contained an older task structure. The later migrations:

- Added the required `task_name` and `status` fields.
- Copied old task names into `task_name`.
- Converted old completion values into statuses.
- Removed obsolete `title`, `priority`, and `completed` columns.

The final application uses only the assignment-required fields.

## 8. Overdue Tasks

A task is considered overdue when:

1. It has a due date.
2. The due date is before today.
3. The status is not Completed.

Overdue status is calculated dynamically instead of being stored as another database field. This prevents stale overdue values.

Example logic:

```php
Task::where('status', '!=', 'Completed')
    ->whereDate('due_date', '<', now()->toDateString())
    ->count();
```

Completed tasks are not shown as overdue even if their due date has already passed.

## 9. User Interface

The main UI is in `resources/views/tasks/index.blade.php`.

It contains:

- Application header
- Current date
- Pending count
- In Progress count
- Completed count
- Overdue count
- Task list
- Inline status dropdown
- Edit action
- Delete action
- Add/edit task form

The interface uses a light, responsive dashboard layout with Tailwind CSS. On smaller screens, task content and form controls stack vertically.

The status dropdown submits automatically when changed:

```html
<select onchange="this.form.submit()">
```

This lets users update a task without opening the edit form.

## 10. Security and Validation

Laravel provides several protections used in the application:

- `@csrf` protects forms against cross-site request forgery.
- Request validation rejects invalid user input.
- `$fillable` limits mass assignment.
- Route model binding ensures task IDs resolve to actual task records.
- Blade escapes displayed values using `{{ }}`.

## 11. Testing

Feature tests are located at `tests/Feature/TaskManagementTest.php`.

The tests verify:

1. A user can create a Pending task.
2. A user can update a task to Completed.
3. A user can delete a task.
4. A user can create an In Progress task.

Run the tests with:

```bash
php artisan test --filter=TaskManagementTest
```

The current test result is:

```text
3 tests passed
12 assertions
```

The frontend can be built with:

```bash
npm run build
```

## 12. Demonstration Flow

During a project presentation, demonstrate the following:

1. Open `/tasks`.
2. Explain the four dashboard counters.
3. Add a task with a name, description, status, and due date.
4. Show that the task appears in the list.
5. Change the task status using the dropdown.
6. Edit the task details.
7. Create a task with a past due date.
8. Show the overdue label and counter.
9. Delete a task.
10. Explain that each action updates the SQLite database.

## 13. Common Questions

### Why did you use SQLite?

SQLite is simple for local development and does not require a separate database server. Laravel migrations and Eloquent work with it directly.

### Why is status stored as text?

The application has a small set of readable status values. Laravel validation ensures that only Pending, In Progress, or Completed can be stored.

### Why is overdue not stored in the database?

Overdue depends on the current date and task status, so it is calculated dynamically. Storing it separately could make it inaccurate.

### Why is there a separate status route?

Status changes are a focused update. The dedicated PATCH route avoids sending unrelated task fields when only the status changes.

### Why use migrations?

Migrations make the database structure reproducible. Another developer can run `php artisan migrate` to create the same schema.

### What happens when validation fails?

Laravel redirects the user back to the form, preserves the submitted values, and displays validation errors using Blade's `@error` directive.

## 14. One-Minute Summary

This is a Laravel Personal Task Manager using Blade, Tailwind CSS, Eloquent, and SQLite. Users can create, view, edit, delete, and update tasks. Each task has a name, description, status, and optional due date. The controller validates requests and handles CRUD operations, while the model manages database interaction. The dashboard calculates task statistics and detects overdue tasks dynamically. Status changes use a dedicated PATCH route and dropdown. Feature tests verify task creation, status changes, deletion, and the In Progress workflow.
