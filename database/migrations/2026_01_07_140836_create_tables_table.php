<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tables', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Např. "Stůl 1 (Výloha)"
            $table->integer('capacity'); // 3 nebo 5
            $table->enum('location', ['indoor', 'garden'])->default('indoor'); // Uvnitř / Zahrádka
            $table->boolean('is_active')->default(true); // Pro případ rozbitého stolu
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tables');
    }
};
