<?php

namespace App\Console\Commands;

use App\Services\StoryousService;
use App\Settings\StoryousSettings;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class StoryousDebug extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'storyous:debug';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Debug Storyous API connection and data fetching';

    /**
     * Execute the console command.
     */
    public function handle(StoryousSettings $settings, StoryousService $service)
    {
        $this->info('Starting Storyous Debug...');

        // 1. Check Settings
        $this->info('1. Checking Settings:');
        $this->line("Client ID: " . ($settings->client_id ? 'SET' : 'MISSING'));
        $this->line("Client Secret: " . ($settings->client_secret ? 'SET' : 'MISSING'));
        $this->line("Merchant ID: " . ($settings->merchant_id ?? 'MISSING'));
        $this->line("Place ID: " . ($settings->place_id ?? 'MISSING'));

        if (!$settings->client_id || !$settings->client_secret || !$settings->merchant_id) {
            $this->error('Missing critical settings! Please configure them in Admin.');
            return;
        }

        // 2. Auth Test
        $this->info(PHP_EOL . '2. Testing Authentication (Get Token)...');
        $token = $service->getAccessToken();

        if ($token) {
            $this->info('SUCCESS: Token received.');
            // $this->line('Token: ' . substr($token, 0, 10) . '...');
        } else {
            $this->error('FAILED: Could not retrieve Access Token. Check Client ID/Secret.');
            return;
        }

        // 3. Data Fetch Test
        $this->info(PHP_EOL . '3. Testing Data Fetch (/bills) for TODAY...');

        $date = now();
        // Use the exact same logic as the service manually to inspect the response
        $createdFrom = $date->copy()->startOfDay()->toIso8601String();
        $createdTo = $date->copy()->endOfDay()->toIso8601String();

        $this->line("Time Range: $createdFrom - $createdTo");
        $this->line("Endpoint: https://api.storyous.com/bills");

        try {
            $response = Http::withToken($token)
                ->get("https://api.storyous.com/bills", [
                    'merchantId' => $settings->merchant_id,
                    'placeId' => $settings->place_id,
                    'createdFrom' => $createdFrom,
                    'createdTo' => $createdTo,
                    'limit' => 5, // Just get a few
                ]);

            $this->info("Response Status: " . $response->status());

            if ($response->successful()) {
                $data = $response->json();
                $this->info('Response Body (First 500 chars):');
                $this->line(substr($response->body(), 0, 500));

                $bills = $data['data'] ?? $data;

                if (is_array($bills)) {
                    $count = count($bills);
                    $this->info("Found $count bills.");
                    if ($count > 0) {
                        $firstBill = $bills[0];
                        $this->info('Sample Bill Keys: ' . implode(', ', array_keys($firstBill)));
                        $this->info('Final Price of first bill: ' . ($firstBill['finalPrice'] ?? 'N/A'));
                    }
                } else {
                    $this->error('Response is not an array! Format unexpected.');
                }
            } else {
                $this->error('API Error Response:');
                $this->line($response->body());
            }

        } catch (\Exception $e) {
            $this->error('Exception during request: ' . $e->getMessage());
        }
    }
}
