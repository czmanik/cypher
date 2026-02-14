<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Fix the mangled 'client_id' row
        // The previous migration accidentally picked the first row (id=16, name='client_id')
        // and set its payload to '{"sync_start_date":null}'

        $clientId = DB::table('settings')
            ->where('group', 'storyous')
            ->where('name', 'client_id')
            ->first();

        if ($clientId) {
            $payload = $clientId->payload;
            // If it looks like JSON object with sync_start_date key, reset it.
            if (str_contains($payload, 'sync_start_date')) {
                // We lost the original client_id value, so set it to null or empty string json encoded.
                DB::table('settings')
                    ->where('id', $clientId->id)
                    ->update(['payload' => json_encode(null)]);
            }
        }

        // 2. Insert the correct row for 'sync_start_date' if it doesn't exist
        $syncDate = DB::table('settings')
            ->where('group', 'storyous')
            ->where('name', 'sync_start_date')
            ->first();

        if (!$syncDate) {
            DB::table('settings')->insert([
                'group' => 'storyous',
                'name' => 'sync_start_date',
                'locked' => false,
                'payload' => json_encode('2026-01-01'), // Correct JSON encoded string value
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            // Ensure the payload is correct if it exists
            DB::table('settings')
                ->where('id', $syncDate->id)
                ->update(['payload' => json_encode('2026-01-01')]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove the sync_start_date row
        DB::table('settings')
            ->where('group', 'storyous')
            ->where('name', 'sync_start_date')
            ->delete();
    }
};
