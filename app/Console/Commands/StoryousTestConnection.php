<?php

namespace App\Console\Commands;

use App\Services\StoryousService;
use App\Settings\StoryousSettings;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class StoryousTestConnection extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'storyous:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test connection to Storyous API and debug data loading issues.';

    /**
     * Execute the console command.
     */
    public function handle(StoryousService $service, StoryousSettings $settings)
    {
        $this->info('Starting Storyous Connection Test...');

        // 1. Check Settings
        $this->info('Checking Settings...');
        $headers = ['Setting', 'Value', 'Status'];
        $data = [
            ['Client ID', $settings->client_id ? '******' : 'MISSING', $settings->client_id ? 'OK' : 'FAIL'],
            ['Client Secret', $settings->client_secret ? '******' : 'MISSING', $settings->client_secret ? 'OK' : 'FAIL'],
            ['Merchant ID', $settings->merchant_id, $settings->merchant_id ? 'OK' : 'FAIL'],
            ['Place ID', $settings->place_id, $settings->place_id ? 'OK' : 'FAIL'],
        ];
        $this->table($headers, $data);

        if (!$settings->client_id || !$settings->client_secret || !$settings->merchant_id || !$settings->place_id) {
            $this->error('Missing required settings. Please configure them in the admin panel or database.');
            return;
        }

        // 2. Test Authentication
        $this->info("\nTesting Authentication...");
        $token = $service->getAccessToken();

        if ($token) {
            $this->info('Authentication SUCCESS. Token obtained.');
        } else {
            $this->error('Authentication FAILED. Check logs for details.');
            return;
        }

        // 3. Test Current Service Logic (Get Bills)
        $this->info("\nTesting Current Service Logic (Fetch Bills for Today)...");
        $today = Carbon::today();

        // Force clear cache for today to ensure we test the API, not the cache
        $cacheKey = 'storyous_bills_' . $today->format('Y-m-d');
        \Illuminate\Support\Facades\Cache::forget($cacheKey);
        $this->info("(Cache cleared for key: {$cacheKey})");

        // We use a try-catch block to handle potential crashes in the service
        try {
            // Use reflection or just call the public method
            $bills = $service->getBillsForDate($today);
            $this->info('Service returned ' . count($bills) . ' bills.');
            if (empty($bills)) {
                 $this->warn('No bills returned. This might be correct if no sales today, or an API error.');
            }
        } catch (\Exception $e) {
            $this->error('Service execution threw exception: ' . $e->getMessage());
        }

        // 4. Test Documented API Logic (Direct HTTP call)
        $this->info("\nTesting Documented API Logic (/bills/{sourceId})...");
        $sourceId = "{$settings->merchant_id}-{$settings->place_id}";
        $url = "https://api.storyous.com/bills/{$sourceId}";

        $this->line("URL: $url");
        $queryParams = [
            'from' => $today->startOfDay()->toIso8601String(),
            'till' => $today->endOfDay()->toIso8601String(),
            'limit' => 5, // Small limit for test
        ];
        $this->line("Params: " . json_encode($queryParams));

        $response = Http::withToken($token)->get($url, $queryParams);

        $this->line("Status Code: " . $response->status());

        if ($response->successful()) {
            $this->info('Documented API Call SUCCESS.');
            $data = $response->json();
            $items = $data['data'] ?? [];
            $this->info('Found ' . count($items) . ' bills (limit 5).');
            if (!empty($items)) {
                $this->line('First bill ID: ' . ($items[0]['billId'] ?? 'N/A'));
            }
        } else {
            $this->error('Documented API Call FAILED.');
            $this->line('Body: ' . $response->body());
        }

        // 5. Compare with "Old/Broken Logic" to demonstrate why the fix was needed
        $this->info("\nSimulating Old/Broken Logic (Raw Request)...");
        $oldUrl = "https://api.storyous.com/bills";
        $oldParams = [
            'merchantId' => $settings->merchant_id,
            'placeId' => $settings->place_id,
            'createdFrom' => $today->startOfDay()->toIso8601String(),
            'createdTo' => $today->endOfDay()->toIso8601String(),
            'limit' => 5,
        ];
        $this->line("URL: $oldUrl");
        $this->line("Params: " . json_encode($oldParams));

        $responseOld = Http::withToken($token)->get($oldUrl, $oldParams);
        $this->line("Status Code: " . $responseOld->status());
        if (!$responseOld->successful()) {
             $this->error('Old Logic Call FAILED (Expected).');
             $this->line('Body: ' . $responseOld->body());
        } else {
             $this->info('Old Logic Call SUCCESS (Unexpected).');
        }

        $this->info("\nTest Complete.");
    }
}
