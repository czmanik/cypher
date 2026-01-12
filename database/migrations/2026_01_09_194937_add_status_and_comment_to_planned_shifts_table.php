<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('planned_shifts', function (Blueprint $table) {
             if (!Schema::hasColumn('planned_shifts', 'status')) {
                $table->string('status')->default('pending')->after('is_published'); // 'pending', 'confirmed', 'request_change'
             }
             if (!Schema::hasColumn('planned_shifts', 'employee_comment')) {
                $table->text('employee_comment')->nullable()->after('status');
             }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('planned_shifts', function (Blueprint $table) {
             if (Schema::hasColumn('planned_shifts', 'status')) {
                $table->dropColumn('status');
             }
             if (Schema::hasColumn('planned_shifts', 'employee_comment')) {
                $table->dropColumn('employee_comment');
             }
        });
    }
};
