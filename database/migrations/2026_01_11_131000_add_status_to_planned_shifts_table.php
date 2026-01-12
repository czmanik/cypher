<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('planned_shifts', function (Blueprint $table) {
            // Statuses:
            // draft: Only manager sees (was !is_published)
            // ordered: Manager assigned, mandatory
            // offered: Manager proposed, waiting for employee acceptance
            // requested: Employee requested availability
            // rejected: Request rejected
            // confirmed: Employee accepted offer (effectively same as ordered, but keeping track)

            $table->string('status')->default('draft')->after('user_id');

            // Note: We are keeping is_published for backward compatibility if needed,
            // or we can drop it. The new logic will rely on status.
            // Let's migrate existing data:
            // if is_published = 1 -> status = 'ordered' (assuming simple assignment previously)
            // if is_published = 0 -> status = 'draft'
        });

        DB::statement("UPDATE planned_shifts SET status = 'ordered' WHERE is_published = 1");
        DB::statement("UPDATE planned_shifts SET status = 'draft' WHERE is_published = 0");

        Schema::table('planned_shifts', function (Blueprint $table) {
            $table->dropColumn('is_published');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('planned_shifts', function (Blueprint $table) {
            $table->boolean('is_published')->default(false);
            $table->dropColumn('status');
        });
    }
};
