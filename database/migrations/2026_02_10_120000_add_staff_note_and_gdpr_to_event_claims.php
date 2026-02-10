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
        Schema::table('event_claims', function (Blueprint $table) {
            $table->text('staff_note')->nullable()->after('redeemed_at');
            $table->boolean('gdpr_consent')->default(false)->after('staff_note');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_claims', function (Blueprint $table) {
            $table->dropColumn(['staff_note', 'gdpr_consent']);
        });
    }
};
