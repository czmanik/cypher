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

    // Base URL for Storyous API (This needs to be confirmed based on documentation)
    // Common one is https://api.storyous.com
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
        // 1. Zkontrolujeme, zda máme API klíč
        if (empty($this->settings->api_key) && empty($this->settings->client_id)) {
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
        if (empty($this->settings->api_key) && empty($this->settings->client_id)) {
            return false;
        }

        // Zkoušíme zavolat endpoint pro ověření (např. seznam poboček nebo merchants)
        // Pokud endpoint selže (404, 401), vrátíme false.
        try {
            // Placeholder: Zkuste endpoint /merchants nebo /auth/check
            // Upravte URL podle dokumentace Storyous API
            $response = Http::withToken($this->settings->api_key ?? '')
                ->timeout(5)
                ->get("{$this->baseUrl}/merchants");

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
        // Placeholder pro reálné API volání
        // Pokud neznáme přesné URL, vrátíme 0.0, abychom nerozbili aplikaci.
        // Až bude znám endpoint, odkomentujte blok níže:

        /*
        try {
            $response = Http::withToken($this->settings->api_key) // Nebo Bearer token z OAuth
                ->get("{$this->baseUrl}/bills", [
                    'merchantId' => $this->settings->merchant_id,
                    'date' => $date->format('Y-m-d'),
                ]);

            if ($response->successful()) {
                // Zde bychom sečetli tržby z odpovědi
                // $data = $response->json();
                // return collect($data)->sum('total_amount');
                return 12345.00; // Mock hodnota
            }

            Log::error('Storyous API error: ' . $response->body());
        } catch (\Exception $e) {
            Log::error('Storyous API exception: ' . $e->getMessage());
        }
        */

        return 0.0;
    }
}
