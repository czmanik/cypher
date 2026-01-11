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
            // Make user_id nullable for "Open Shifts"
            $table->foreignId('user_id')->nullable()->change();

            // Add bonus field (Manager can add bonus to shift)
            $table->decimal('bonus', 8, 2)->nullable()->default(0)->after('end_at');

            // Add target_employee_type to clarify who can pick this open shift
            // (Only needed if shift_role is not enough or if we want to target multiple types)
            // But user said "shift_role" is already used for position.
            // Let's rely on shift_role, but I'll add this just in case if needed for filtering flexibility
            // Actually, let's keep it simple and reuse shift_role as the "target"
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('planned_shifts', function (Blueprint $table) {
            // Revert changes
             // Note: changing nullable back to not null might fail if there are nulls.
            // We assume this is dev.
            $table->foreignId('user_id')->nullable(false)->change();
            $table->dropColumn('bonus');
        });
    }
};
