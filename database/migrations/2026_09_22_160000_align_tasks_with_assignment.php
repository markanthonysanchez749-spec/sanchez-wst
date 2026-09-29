<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('tasks')) {
            return;
        }

        $columns = DB::getSchemaBuilder()->getColumnListing('tasks');

        if (! in_array('task_name', $columns, true)) {
            DB::statement('ALTER TABLE tasks ADD COLUMN task_name TEXT NOT NULL DEFAULT ""');
        }

        if (! in_array('status', $columns, true)) {
            DB::statement('ALTER TABLE tasks ADD COLUMN status TEXT NOT NULL DEFAULT "Pending"');
        }

        if (in_array('title', $columns, true)) {
            DB::statement('UPDATE tasks SET task_name = title WHERE task_name = ""');
        }

        if (in_array('completed', $columns, true)) {
            DB::statement('UPDATE tasks SET status = CASE WHEN completed = 1 THEN "Completed" ELSE "Pending" END');
        }
    }

    public function down(): void
    {
        // Keep the repair migration non-destructive for local development.
    }
};