<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Úprava Uživatelů
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_manager')->default(false)->after('is_active'); // Je to šéf?
            $table->string('employee_type')->nullable()->after('salary_type'); // 'kitchen', 'floor', 'support'
        });

        // 2. Úprava Checklistů (Cílení)
        Schema::table('checklist_templates', function (Blueprint $table) {
            // Na koho to cílí? 'all' (všichni), 'type' (oddělení), 'user' (osoba)
            $table->string('target_type')->default('all')->after('task_name'); 
            
            // Pokud cílí na oddělení:
            $table->string('target_employee_type')->nullable()->after('target_type');
            
            // Pokud cílí na konkrétního Pepu:
            $table->foreignId('target_user_id')->nullable()->after('target_employee_type')->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('checklist_templates', function (Blueprint $table) {
            $table->dropForeign(['target_user_id']);
            $table->dropColumn(['target_type', 'target_employee_type', 'target_user_id']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['is_manager', 'employee_type']);
        });
    }
};
