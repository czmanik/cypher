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
        Schema::table('work_shifts', function (Blueprint $table) {
            $table->foreignId('planned_shift_id')->nullable()->after('user_id')->constrained('planned_shifts')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('work_shifts', function (Blueprint $table) {
            $table->dropForeign(['planned_shift_id']);
            $table->dropColumn('planned_shift_id');
        });
    }
};
