<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            // Vazba na stůl. Když smažeš stůl, smažou se i jeho rezervace.
            $table->foreignId('table_id')->constrained()->cascadeOnDelete();
            
            // Kontakt na zákazníka
            $table->string('customer_name');
            $table->string('customer_email')->nullable();
            $table->string('customer_phone');
            
            // Čas a detaily
            $table->dateTime('reservation_time'); // Datum a čas začátku
            $table->integer('duration_minutes')->default(90); // Defaultně 1.5 hodiny
            $table->integer('guests_count'); // Kolik lidí reálně přijde
            
            // Stav rezervace
            $table->enum('status', ['pending', 'confirmed', 'cancelled', 'completed'])
                ->default('pending');
                
            $table->text('note')->nullable(); // Poznámka (alergie, dětská židle...)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
