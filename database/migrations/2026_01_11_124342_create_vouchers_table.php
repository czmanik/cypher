<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // Unikátní kód (např. QR obsah)
            $table->decimal('value', 10, 2)->nullable(); // Hodnota (např. 500 Kč)
            $table->timestamp('used_at')->nullable(); // Kdy byl použit (pokud NULL, je platný)
            // Kdo ho aktivoval (zaměstnanec)
            $table->foreignId('used_by_user_id')->nullable()->constrained('users'); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vouchers');
    }
};
