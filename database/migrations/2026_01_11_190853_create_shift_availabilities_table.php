<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shift_availabilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Availability can be a range (e.g. "Whole June") or single days.
            $table->date('start_date');
            $table->date('end_date');

            // Type of availability: 'available' (I can work), 'unavailable' (I cannot work)
            // User request implies "Jsem dostupný" (I am available).
            // But usually users mark "vacation" (unavailable).
            // The prompt says: "Uživatel nyní může zadat že má volno například celý měsíc" -> "has free time" -> Available?
            // "pepa má volno celý měsíc, tak si ho můžu rovnou přiřazovat" -> "Pepa is free/available all month, so I can assign him".
            // So this table tracks WHEN THEY CAN WORK.

            $table->text('note')->nullable(); // "Only mornings", etc.

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shift_availabilities');
    }
};
