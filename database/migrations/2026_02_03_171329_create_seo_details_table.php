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
        Schema::create('seo_details', function (Blueprint $table) {
            $table->id();
            $table->morphs('seoable'); // seoable_type, seoable_id
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->string('image')->nullable(); // Path to image
            $table->string('robots')->default('index, follow');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seo_details');
    }
};
