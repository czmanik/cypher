<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('planned_shifts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            
            $table->dateTime('start_at');
            $table->dateTime('end_at');
            
            // Můžeme specifikovat roli pro tuto konkrétní směnu (např. Karel může jít jednou na Bar, podruhé na Plac)
            $table->string('shift_role')->nullable(); // 'bar', 'kitchen', 'floor'
            
            $table->string('color')->nullable(); // Barvička v kalendáři
            $table->text('note')->nullable();
            
            // Draft systém: Dokud to nezveřejníš, vidíš to jen ty
            $table->boolean('is_published')->default(false); 
            
            $table->timestamps();
        });
    }
};
