<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->string('storyous_id')->nullable()->unique()->after('slug');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->string('storyous_id')->nullable()->unique()->after('id');
            $table->decimal('vat_rate', 5, 2)->default(0)->after('price');
        });
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('storyous_id');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['storyous_id', 'vat_rate']);
        });
    }
};
