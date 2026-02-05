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
        // SQLite does not support MODIFY COLUMN in this way.
        // In SQLite, enums are typically stored as TEXT (VARCHAR), so no schema change is strictly needed
        // unless a CHECK constraint exists. Laravel defaults don't usually add CHECK constraints for enums.
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        DB::statement("ALTER TABLE work_shifts MODIFY COLUMN status ENUM('active', 'pending_approval', 'approved', 'rejected', 'paid') NOT NULL DEFAULT 'active'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        // Warning: Changing this back might fail if there are records with 'paid' status.
        // We will attempt to revert it to the original set.
        DB::statement("ALTER TABLE work_shifts MODIFY COLUMN status ENUM('active', 'pending_approval', 'approved', 'rejected') NOT NULL DEFAULT 'active'");
    }
};
