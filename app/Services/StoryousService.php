<?php

namespace App\Services;

use App\Settings\StoryousSettings;
use Carbon\Carbon;
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
     * Získá tržby za daný den.
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

        // 2. Sestavíme URL pro získání účtenek/tržeb
        // POZNÁMKA: Toto je odhad endpointu. Skutečný endpoint závisí na dokumentaci.
        // Typicky: /bills nebo /merchants/{merchantId}/branches/{branchId}/overview

        // Příklad volání (zakomentovaný, dokud neznáme přesné URL):
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

        // Prozatím vracíme 0, dokud nebude integrace dokončena
        return 0.0;
    }

    /**
     * Helper pro testování připojení (vrací true/false)
     */
    public function testConnection(): bool
    {
        // Implementovat ping na API
        return !empty($this->settings->api_key) || !empty($this->settings->client_id);
    }
}
