<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Add 'name' as nullable initially
        Schema::table('recipes', function (Blueprint $table) {
            $table->string('name')->nullable()->after('id');
        });

        // 2. Populate 'name' from associated product
        $recipes = DB::table('recipes')->get();
        foreach ($recipes as $recipe) {
            $productName = DB::table('products')->where('id', $recipe->product_id)->value('name');
            if ($productName) {
                DB::table('recipes')->where('id', $recipe->id)->update(['name' => $productName]);
            }
        }

        // Ensure no nulls exist before making column required
        DB::table('recipes')->whereNull('name')->update(['name' => 'Neznámý recept']);

        // 3. Make 'name' required, 'product_id' nullable, and drop 'allowed_roles'
        Schema::table('recipes', function (Blueprint $table) {
            $table->string('name')->nullable(false)->change();
            $table->foreignId('product_id')->nullable()->change();
            $table->dropColumn('allowed_roles');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('recipes', function (Blueprint $table) {
            $table->json('allowed_roles')->nullable();
            // Note: Reverting product_id to non-nullable might fail if there are records with null product_id.
            // This is acceptable for a down migration in this context.
            $table->foreignId('product_id')->nullable(false)->change();
            $table->dropColumn('name');
        });
    }
};
