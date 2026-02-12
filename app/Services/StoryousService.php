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

    // Base URL for API calls (bills, places, etc.)
    protected string $baseUrl = 'https://api.storyous.com';

    // Base URL for Authentication
    protected string $authUrl = 'https://login.storyous.com/api/auth/authorize';

    public function __construct(StoryousSettings $settings)
    {
        $this->settings = $settings;
    }

    /**
     * Získá přístupový token (Bearer Token) pro komunikaci s API.
     * Token je kešován po dobu své platnosti.
     *
     * @return string|null
     */
    public function getAccessToken(): ?string
    {
        if (empty($this->settings->client_id) || empty($this->settings->client_secret)) {
            Log::warning('Storyous API: Missing Client ID or Secret.');
            return null;
        }

        // Cache klíč: storyous_access_token
        return Cache::remember('storyous_access_token', now()->addMinutes(55), function () {
            try {
                $response = Http::asForm()->post($this->authUrl, [
                    'client_id' => $this->settings->client_id,
                    'client_secret' => $this->settings->client_secret,
                    'grant_type' => 'client_credentials',
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    return $data['access_token'] ?? null;
                }

                Log::error('Storyous Auth Failed: ' . $response->body());
                return null;

            } catch (\Exception $e) {
                Log::error('Storyous Auth Exception: ' . $e->getMessage());
                return null;
            }
        });
    }

    /**
     * Získá tržby za daný den (kešováno).
     *
     * @param Carbon $date
     * @return float
     */
    public function getRevenueForDate(Carbon $date): float
    {
        $bills = $this->getBillsForDate($date);

        return collect($bills)->sum(function ($bill) {
            return $bill['finalPrice'] ?? $bill['totalAmount'] ?? 0.0;
        });
    }

    /**
     * Získá celkové spropitné za daný den.
     *
     * @param Carbon $date
     * @return float
     */
    public function getTipsForDate(Carbon $date): float
    {
        $bills = $this->getBillsForDate($date);

        return collect($bills)->sum(function ($bill) {
            // Tips is usually a string like "10.00"
            return (float) ($bill['tips'] ?? 0.0);
        });
    }

    /**
     * Získá celkový počet hostů za daný den.
     *
     * @param Carbon $date
     * @return int
     */
    public function getPersonCountForDate(Carbon $date): int
    {
        $bills = $this->getBillsForDate($date);

        return collect($bills)->sum(function ($bill) {
            return (int) ($bill['personCount'] ?? 0);
        });
    }

    /**
     * Helper pro testování připojení (vrací true/false).
     * Zkouší získat token. Pokud se to podaří, klíče jsou platné.
     */
    public function testConnection(): bool
    {
        // 1. Vymažeme cache tokenu, abychom otestovali čerstvé přihlášení
        Cache::forget('storyous_access_token');

        // 2. Zkusíme získat token
        $token = $this->getAccessToken();

        if (!$token) {
            return false;
        }

        return true;
    }

    /**
     * Získá seznam účtenek za daný den (kešováno).
     *
     * @param Carbon $date
     * @return array
     */
    public function getBillsForDate(Carbon $date): array
    {
        // 1. Zkontrolujeme, zda máme nezbytné klíče
        if (empty($this->settings->merchant_id) || empty($this->settings->place_id)) {
            Log::warning('Storyous API: Missing Merchant ID or Place ID.', [
                'merchant_id' => $this->settings->merchant_id,
                'place_id' => $this->settings->place_id,
            ]);
            return [];
        }

        // Cache klíč: storyous_bills_YYYY-MM-DD
        $cacheKey = 'storyous_bills_' . $date->format('Y-m-d');

        // Pokud jde o dnešek, kešujeme krátce (např. 15 min), aby se data aktualizovala během dne.
        // Pokud jde o minulost, můžeme kešovat déle (např. 24 hodin), protože se data nemění.
        $ttl = $date->isToday() ? now()->addMinutes(15) : now()->addHours(24);

        return Cache::remember($cacheKey, $ttl, function () use ($date) {
            return $this->fetchBillsFromApi($date);
        });
    }

    /**
     * Získá detail konkrétní účtenky (včetně položek).
     *
     * @param string $billId
     * @return array|null
     */
    public function getBillDetail(string $billId): ?array
    {
        $token = $this->getAccessToken();

        if (!$token) {
            return null;
        }

        $sourceId = "{$this->settings->merchant_id}-{$this->settings->place_id}";
        $url = "{$this->baseUrl}/bills/{$sourceId}/{$billId}";

        try {
            $response = Http::withToken($token)->get($url);

            if ($response->successful()) {
                return $response->json();
            } else {
                Log::error("Storyous API error (Bill Detail) at {$url}: " . $response->body());
            }
        } catch (\Exception $e) {
            Log::error('Storyous API exception (Bill Detail): ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Interní metoda pro reálné volání API (bez cache).
     */
    protected function fetchBillsFromApi(Carbon $date): array
    {
        $token = $this->getAccessToken();

        if (!$token) {
            Log::warning('Storyous API: No access token available.');
            return [];
        }

        // Časové rozmezí pro daný den
        $from = $date->copy()->startOfDay()->toIso8601String();
        $till = $date->copy()->endOfDay()->toIso8601String();

        $sourceId = "{$this->settings->merchant_id}-{$this->settings->place_id}";

        try {
            // Volání API pro získání účtenek
            // Endpoint: /bills/{merchantId}-{placeId}
            // Parametry: from, till
            $url = "{$this->baseUrl}/bills/{$sourceId}";

            Log::info("Storyous API: Fetching bills.", ['url' => $url, 'from' => $from, 'till' => $till, 'limit' => 100]);

            $response = Http::withToken($token)
                ->get($url, [
                    'from' => $from,
                    'till' => $till,
                    'limit' => '100', // Sníženo na 100 a přetypováno na string pro jistotu
                ]);

            if ($response->successful()) {
                $data = $response->json();

                // Storyous API vrací data v poli, nebo v klíči 'data'.
                $bills = $data['data'] ?? $data;

                if (!is_array($bills)) {
                    // Pokud to není pole, může to být chyba nebo prázdný objekt
                    return [];
                }

                return $bills;
            } else {
                Log::error("Storyous API error (Bills) at {$url}: Status {$response->status()}, Body: " . $response->body());
            }

        } catch (\Exception $e) {
            Log::error('Storyous API exception (Bills): ' . $e->getMessage());
        }

        return [];
    }
}
