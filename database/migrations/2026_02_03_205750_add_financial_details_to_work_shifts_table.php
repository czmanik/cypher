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
            $table->decimal('bonus', 10, 2)->default(0)->after('calculated_wage');
            $table->decimal('penalty', 10, 2)->default(0)->after('bonus');
            $table->text('bonus_note')->nullable()->after('manager_note');
            $table->text('penalty_note')->nullable()->after('bonus_note');

            // Zálohy
            $table->decimal('advance_amount', 10, 2)->default(0)->after('penalty');

            // Finální platba
            $table->string('payment_method')->nullable()->after('advance_amount'); // 'cash', 'bank_transfer'
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('work_shifts', function (Blueprint $table) {
            $table->dropColumn([
                'bonus',
                'penalty',
                'bonus_note',
                'penalty_note',
                'advance_amount',
                'payment_method',
            ]);
        });
    }
};
