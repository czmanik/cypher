<?php

namespace App\Services;

use App\Settings\StoryousSettings;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class StoryousService
{
    protected StoryousSettings $settings;

    // TODO: Update this URL once the official Storyous API documentation is confirmed.
    // Common base URL is https://api.storyous.com but it might differ.
    protected string $baseUrl = 'https://api.storyous.com';

    public function __construct(StoryousSettings $settings)
    {
        $this->settings = $settings;
    }

    /**
     * Získá tržby za daný den (kešováno).
     *
     * @param Carbon $date
     * @return float
     */
    public function getRevenueForDate(Carbon $date): float
    {
        // 1. Zkontrolujeme, zda máme nezbytné klíče
        if (empty($this->settings->client_id) || empty($this->settings->merchant_id)) {
            Log::warning('Storyous API credentials are missing.');
            return 0.0;
        }

        // Cache klíč: storyous_revenue_YYYY-MM-DD
        $cacheKey = 'storyous_revenue_' . $date->format('Y-m-d');

        // Pokud jde o dnešek, kešujeme krátce (např. 15 min), aby se data aktualizovala během dne.
        // Pokud jde o minulost, můžeme kešovat déle (např. 24 hodin), protože se data nemění.
        $ttl = $date->isToday() ? now()->addMinutes(15) : now()->addHours(24);

        return Cache::remember($cacheKey, $ttl, function () use ($date) {
            return $this->fetchRevenueFromApi($date);
        });
    }

    /**
     * Helper pro testování připojení (vrací true/false).
     * Zkouší reálný dotaz na API (nebo aspoň validuje klíče).
     */
    public function testConnection(): bool
    {
        if (empty($this->settings->client_id) || empty($this->settings->merchant_id)) {
            return false;
        }

        // Zkoušíme zavolat endpoint pro ověření.
        try {
            // TODO: Replace with the correct "Ping" or "List Merchants" endpoint.
            // Using Basic Auth as a common placeholder for ClientID/Secret.
            // If OAuth is required, this logic needs to exchange credentials for a token first.
            $response = Http::withBasicAuth($this->settings->client_id, $this->settings->client_secret ?? '')
                ->timeout(5)
                ->get("{$this->baseUrl}/merchants/{$this->settings->merchant_id}");

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('Storyous Test Connection Failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Interní metoda pro reálné volání API (bez cache).
     */
    protected function fetchRevenueFromApi(Carbon $date): float
    {
        // TODO: Implement actual API call once documentation is available.
        // Below is a conceptual implementation.

        /*
        try {
            $response = Http::withBasicAuth($this->settings->client_id, $this->settings->client_secret)
                ->get("{$this->baseUrl}/bills", [
                    'merchantId' => $this->settings->merchant_id,
                    'placeId' => $this->settings->place_id,
                    'date' => $date->format('Y-m-d'),
                ]);

            if ($response->successful()) {
                $data = $response->json();
                // TODO: Adjust parsing logic based on actual response structure
                // return collect($data['items'] ?? [])->sum('total_amount');
                return 12345.00;
            }

            Log::error('Storyous API error: ' . $response->body());
        } catch (\Exception $e) {
            Log::error('Storyous API exception: ' . $e->getMessage());
        }
        */

        // Returns 0.0 to prevent crashes until fully implemented.
        return 0.0;
    }
}
