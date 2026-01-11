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
        // SQLite doesn't support changing column type directly in some versions/drivers easily,
        // but Laravel handles it usually. However, conversion from string to json/array needs care.
        // Or we can just keep it as 'text' and cast it.
        // Actually, in SQLite JSON is stored as TEXT.
        // We need to migrate existing data: "kitchen" -> ["kitchen"]

        // 1. Rename old column
        // 2. Create new column
        // 3. Copy data
        // 4. Drop old column

        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('employee_type', 'employee_type_old');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->json('employee_type')->nullable();
        });

        // Migrate data
        DB::table('users')->chunkById(100, function ($users) {
            foreach ($users as $user) {
                $old = $user->employee_type_old;
                $new = $old ? json_encode([$old]) : json_encode([]);
                DB::table('users')
                    ->where('id', $user->id)
                    ->update(['employee_type' => $new]);
            }
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('employee_type_old');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to string (taking first element)
        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('employee_type', 'employee_type_new');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('employee_type')->nullable();
        });

        DB::table('users')->chunkById(100, function ($users) {
            foreach ($users as $user) {
                $json = json_decode($user->employee_type_new, true);
                $first = is_array($json) && count($json) > 0 ? $json[0] : null;
                DB::table('users')
                    ->where('id', $user->id)
                    ->update(['employee_type' => $first]);
            }
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('employee_type_new');
        });
    }
};
