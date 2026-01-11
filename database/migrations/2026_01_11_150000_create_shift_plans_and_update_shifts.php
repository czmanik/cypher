<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shift_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // "Týden 3"
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('status', ['draft', 'published', 'closed'])->default('draft');
            $table->text('note')->nullable();
            $table->timestamps();
        });

        Schema::table('planned_shifts', function (Blueprint $table) {
            // User ID can be null (Open Slot)
            $table->foreignId('user_id')->nullable()->change();

            // Link to a plan (optional but good for grouping)
            $table->foreignId('shift_plan_id')->nullable()->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('planned_shifts', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable(false)->change();
            $table->dropForeign(['shift_plan_id']);
            $table->dropColumn('shift_plan_id');
        });

        Schema::dropIfExists('shift_plans');
    }
};
