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
        Schema::create('inventory_items', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('category')->index(); // 'ingredient', 'operational'
            $table->decimal('stock_qty', 10, 4)->default(0); // Using 4 decimals for precision (e.g. 0.005 kg)
            $table->string('unit'); // 'kg', 'l', 'ks', etc.
            $table->decimal('package_size', 10, 4)->default(1); // How much to deduct per "click" or add per "package"
            $table->decimal('min_stock_qty', 10, 4)->default(0);
            $table->decimal('price', 10, 2)->default(0); // Unit price
            $table->timestamps();
        });

        Schema::create('inventory_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventory_item_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type'); // 'purchase', 'write_off', 'adjustment'
            $table->decimal('quantity', 10, 4); // + or - amount
            $table->decimal('cost', 10, 2)->default(0); // Cost at time of transaction
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_transactions');
        Schema::dropIfExists('inventory_items');
    }
};
