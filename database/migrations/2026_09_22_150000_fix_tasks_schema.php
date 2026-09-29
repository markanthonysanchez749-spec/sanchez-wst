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

        if (! in_array('title', $columns, true)) {
            DB::statement('ALTER TABLE tasks ADD COLUMN title TEXT NOT NULL DEFAULT ""');
        }

        if (! in_array('description', $columns, true)) {
            DB::statement('ALTER TABLE tasks ADD COLUMN description TEXT NULL');
        }

        if (! in_array('priority', $columns, true)) {
            DB::statement('ALTER TABLE tasks ADD COLUMN priority TEXT NOT NULL DEFAULT "medium"');
        }

        if (! in_array('due_date', $columns, true)) {
            DB::statement('ALTER TABLE tasks ADD COLUMN due_date DATE NULL');
        }

        if (! in_array('completed', $columns, true)) {
            DB::statement('ALTER TABLE tasks ADD COLUMN completed BOOLEAN NOT NULL DEFAULT 0');
        }
    }

    public function down(): void
    {
        // No-op to avoid destructive rollback in development.
    }
};
