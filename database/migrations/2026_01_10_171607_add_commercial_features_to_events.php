<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Rozšíření Akcí o obchodní logiku
        Schema::table('events', function (Blueprint $table) {
            $table->boolean('is_commercial')->default(false); // Je to akce s registrací/slevou?
            $table->integer('capacity_limit')->nullable();    // Kolik lidí to může využít (např. 100)
            
            // Nastavení formuláře: Která pole chceme po uživateli? (uložíme jako JSON)
            // Příklad: ['email', 'instagram']
            $table->json('required_fields')->nullable(); 
        });

        // 2. Tabulka pro sběr kontaktů a nároků
        Schema::create('event_claims', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            
            // Data o návštěvníkovi (nemusí být registrovaný uživatel)
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('instagram')->nullable();
            
            // Technické věci pro QR kód
            $table->string('claim_token')->unique(); // Unikátní kód (hash)
            $table->timestamp('redeemed_at')->nullable(); // Kdy to uplatnil na baru
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_claims');
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn(['is_commercial', 'capacity_limit', 'required_fields']);
        });
    }
};
