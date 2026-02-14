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
        $setting = DB::table('settings')->where('group', 'storyous')->first();

        if ($setting) {
            $payload = json_decode($setting->payload, true);

            if (is_string($payload)) {
                $payload = json_decode($payload, true);
            }

            if (!is_array($payload)) {
                $payload = [];
            }

            // Add the missing key if it doesn't exist
            if (!array_key_exists('sync_start_date', $payload)) {
                $payload['sync_start_date'] = '2026-01-01'; // Default value per user request

                DB::table('settings')
                    ->where('id', $setting->id)
                    ->update(['payload' => json_encode($payload)]);
            }
        } else {
            // Create a default one if it doesn't exist
            DB::table('settings')->insert([
                'group' => 'storyous',
                'name' => 'storyous',
                'locked' => false,
                'payload' => json_encode([
                    'client_id' => null,
                    'client_secret' => null,
                    'merchant_id' => null,
                    'place_id' => null,
                    'sync_start_date' => '2026-01-01'
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $setting = DB::table('settings')->where('group', 'storyous')->first();

        if ($setting) {
            $payload = json_decode($setting->payload, true);

            if (is_string($payload)) {
                $payload = json_decode($payload, true);
            }

            if (is_array($payload) && array_key_exists('sync_start_date', $payload)) {
                unset($payload['sync_start_date']);

                DB::table('settings')
                    ->where('id', $setting->id)
                    ->update(['payload' => json_encode($payload)]);
            }
        }
    }
};
