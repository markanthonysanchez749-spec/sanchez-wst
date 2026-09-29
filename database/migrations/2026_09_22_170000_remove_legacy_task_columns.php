<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('tasks')) {
            return;
        }

        $legacyColumns = collect(['title', 'priority', 'completed'])
            ->filter(fn (string $column): bool => Schema::hasColumn('tasks', $column))
            ->values()
            ->all();

        if ($legacyColumns !== []) {
            Schema::table('tasks', function (Blueprint $table) use ($legacyColumns): void {
                $table->dropColumn($legacyColumns);
            });
        }
    }

    public function down(): void
    {
        // Legacy fields are intentionally not restored.
    }
};