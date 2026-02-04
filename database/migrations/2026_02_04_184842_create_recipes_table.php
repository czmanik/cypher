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
        Schema::create('recipes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('yield')->nullable(); // e.g. "4 porce"
            $table->string('prep_time')->nullable(); // e.g. "45 min"
            $table->string('temperature')->nullable(); // e.g. "180 °C"
            $table->string('video_url')->nullable(); // Youtube/Vimeo link
            $table->longText('description')->nullable(); // Postup
            $table->json('ingredients')->nullable(); // [{name: 'Maso', amount: '1kg'}, ...]
            $table->json('allowed_roles')->nullable(); // ['kitchen', 'floor']
            $table->json('images')->nullable(); // Gallery paths
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recipes');
    }
};
