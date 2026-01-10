<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('menu_items', function (Blueprint $table) {
            $table->id();
            $table->string('label'); // Text tlačítka (např. "O nás")
            
            // Typ odkazu: 'page' (stránka), 'route' (systém), 'url' (externí)
            $table->string('type')->default('page'); 
            
            // Vazby na různé cíle
            $table->foreignId('page_id')->nullable()->constrained()->nullOnDelete();
            $table->string('route_name')->nullable(); // např. 'reservations.create'
            $table->string('url')->nullable();        // např. 'https://instagram.com/...'
            
            $table->integer('sort_order')->default(0); // Pro řazení v menu
            $table->boolean('new_tab')->default(false); // Otevřít v novém okně?
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menu_items');
    }
};
