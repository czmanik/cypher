<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bills', function (Blueprint $table) {
            $table->id();
            $table->string('storyous_bill_id')->unique();
            $table->string('bill_number')->nullable();
            $table->dateTime('paid_at');
            $table->decimal('total_amount', 10, 2);
            $table->string('currency', 3)->default('CZK');
            $table->string('table_number')->nullable();
            $table->integer('person_count')->default(0);
            $table->json('raw_data')->nullable();
            $table->timestamps();
        });

        Schema::create('bill_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bill_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
            $table->string('storyous_product_id')->nullable();
            $table->string('name');
            $table->decimal('quantity', 10, 4);
            $table->decimal('price_per_unit', 10, 2);
            $table->decimal('total_price', 10, 2);
            $table->decimal('vat_rate', 5, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bill_items');
        Schema::dropIfExists('bills');
    }
};
