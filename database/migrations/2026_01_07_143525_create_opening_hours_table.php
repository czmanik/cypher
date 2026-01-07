<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('opening_hours', function (Blueprint $table) {
            $table->id();
            $table->integer('day_of_week'); // 1 = Pondělí, 7 = Neděle
            
            // Bar / Kavárna
            $table->time('bar_open')->nullable();
            $table->time('bar_close')->nullable();
            
            // Kuchyně (může zavírat dřív)
            $table->time('kitchen_open')->nullable();
            $table->time('kitchen_close')->nullable();
            
            $table->boolean('is_closed')->default(false); // Pro dny, kdy je úplně zavřeno
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('opening_hours');
    }
};
