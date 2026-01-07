<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('content_blocks', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique(); // Identifikátor (např. 'homepage_hero', 'about_us')
            $table->string('title'); // Nadpis bloku
            $table->longText('content')->nullable(); // Text s HTML (obrázky, formátování)
            $table->string('image_path')->nullable(); // Volitelný hlavní obrázek (např. na pozadí)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('content_blocks');
    }
};
