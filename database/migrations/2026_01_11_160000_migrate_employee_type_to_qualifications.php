<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Add new column
        Schema::table('users', function (Blueprint $table) {
            $table->json('qualifications')->nullable()->after('employee_type');
        });

        // 2. Migrate data
        // We need to fetch users and convert string to json array manually via PHP because SQLite/MySQL differ in JSON functions
        // But for raw SQL in migration:

        $users = DB::table('users')->whereNotNull('employee_type')->get();
        foreach ($users as $user) {
            $val = $user->employee_type ? [$user->employee_type] : [];
            DB::table('users')->where('id', $user->id)->update([
                'qualifications' => json_encode($val)
            ]);
        }

        // 3. Drop old column
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('employee_type');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('employee_type')->nullable()->after('qualifications');
        });

        // Revert data (take first item)
        $users = DB::table('users')->whereNotNull('qualifications')->get();
        foreach ($users as $user) {
            $arr = json_decode($user->qualifications, true);
            $val = is_array($arr) && count($arr) > 0 ? $arr[0] : null;
            DB::table('users')->where('id', $user->id)->update([
                'employee_type' => $val
            ]);
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('qualifications');
        });
    }
};
