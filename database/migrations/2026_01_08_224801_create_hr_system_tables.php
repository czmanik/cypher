<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Rozšíření tabulky UŽIVATELŮ (PIN a Mzda)
        Schema::table('users', function (Blueprint $table) {
            $table->string('pin_code', 4)->nullable()->unique(); // 4-místný PIN
            $table->decimal('hourly_rate', 10, 2)->nullable();   // Sazba (Kč)
            $table->enum('salary_type', ['hourly', 'fixed'])->default('hourly'); // Typ mzdy
            $table->boolean('is_active')->default(true); // Pro deaktivaci bývalých zaměstnanců
        });

        // 2. ŠABLONY CHECKLISTU (To, co nastavuješ ty v adminu)
        Schema::create('checklist_templates', function (Blueprint $table) {
            $table->id();
            $table->string('task_name'); // Např. "Zhasnout světla"
            $table->boolean('is_required')->default(true); // Musí to splnit?
            $table->integer('sort_order')->default(0); // Pořadí
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 3. PRACOVNÍ SMĚNY (Hlavní tabulka)
        Schema::create('work_shifts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            
            $table->dateTime('start_at');
            $table->dateTime('end_at')->nullable(); // Null = směna právě běží
            
            // Status směny
            $table->enum('status', ['active', 'pending_approval', 'approved', 'rejected'])->default('active');
            
            // Finance (vypočítané po skončení)
            $table->decimal('total_hours', 8, 2)->default(0);
            $table->decimal('calculated_wage', 10, 2)->default(0);
            
            // Poznámky
            $table->text('general_note')->nullable(); // "Co se stalo"
            $table->text('manager_note')->nullable(); // Tvoje poznámka
            
            $table->timestamps();
        });

        // 4. REPORTY ZE SMĚNY (Všechny incidenty: Nákup, Odpis, Údržba, Výdaj)
        Schema::create('shift_report_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('work_shift_id')->constrained()->cascadeOnDelete();
            
            // Typ položky
            $table->enum('type', [
                'expense',      // Výdaj z kasy (hotovost)
                'purchase',     // Nákupní seznam (co se má koupit)
                'waste',        // Odpis / Ztráta
                'maintenance',  // Údržba / Závada
            ]);
            
            $table->string('item_name'); // "Limetky" nebo "Oprava záchodu"
            $table->decimal('amount', 10, 2)->nullable(); // Cena (pro výdaje)
            $table->decimal('quantity', 8, 2)->nullable(); // Množství (pro odpisy/nákup)
            $table->string('photo_path')->nullable(); // Fotka účtenky nebo závady
            
            $table->timestamps();
        });

        // 5. VÝSLEDKY CHECKLISTU (Co reálně odklikali)
        Schema::create('shift_checklist_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('work_shift_id')->constrained()->cascadeOnDelete();
            $table->string('task_name'); // Ukládáme název, kdyby se šablona později změnila
            $table->boolean('is_completed')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shift_checklist_results');
        Schema::dropIfExists('shift_report_items');
        Schema::dropIfExists('work_shifts');
        Schema::dropIfExists('checklist_templates');
        
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['pin_code', 'hourly_rate', 'salary_type', 'is_active']);
        });
    }
};