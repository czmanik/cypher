<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shift_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('planned_shift_id')->constrained()->cascadeOnDelete();

            // Who performed the action (Manager or Employee)
            $table->foreignId('user_id')->constrained();

            // Action type: 'created', 'assigned', 'claimed', 'approved', 'rejected', 'bonus_added', 'paid'
            $table->string('action');

            $table->json('payload')->nullable(); // Stores changes (e.g. old status -> new status, bonus amount)

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shift_audit_logs');
    }
};
