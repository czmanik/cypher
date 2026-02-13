<?php

namespace App\Services;

use App\Settings\StoryousSettings;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

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
     * Import menu (Categories and Products) from Storyous.
     *
     * @return array Stats about imported items
     */
    public function importMenu(): array
    {
        $token = $this->getAccessToken();
        if (!$token) {
            return ['status' => 'error', 'message' => 'No access token'];
        }

        $sourceId = "{$this->settings->merchant_id}-{$this->settings->place_id}";

        // 1. Fetch Categories
        $catsResponse = Http::withToken($token)->get("{$this->baseUrl}/stock/{$sourceId}/categories");
        if (!$catsResponse->successful()) {
            return ['status' => 'error', 'message' => 'Failed to fetch categories: ' . $catsResponse->status()];
        }
        $categoriesData = $catsResponse->json()['data'] ?? $catsResponse->json();

        // 2. Fetch Products
        $prodsResponse = Http::withToken($token)->get("{$this->baseUrl}/stock/{$sourceId}/products");
        if (!$prodsResponse->successful()) {
            return ['status' => 'error', 'message' => 'Failed to fetch products: ' . $prodsResponse->status()];
        }
        $productsData = $prodsResponse->json()['data'] ?? $prodsResponse->json();

        $stats = [
            'categories_created' => 0,
            'categories_updated' => 0,
            'products_created' => 0,
            'products_updated' => 0,
            'products_renamed_old' => 0,
        ];

        $categoryMap = [];

        // Process Categories
        foreach ($categoriesData as $catItem) {
            $sId = $catItem['_id'] ?? $catItem['categoryId'] ?? null;
            if (!$sId) continue;

            $name = $catItem['name'] ?? 'Unknown Category';

            $category = \App\Models\Category::where('storyous_id', $sId)->first();

            if ($category) {
                // Update existing category
                $category->update(['name' => $name]);
                $stats['categories_updated']++;
            } else {
                // Create new category (hidden)
                $category = \App\Models\Category::create([
                    'storyous_id' => $sId,
                    'name' => $name,
                    'slug' => Str::slug($name) . '-' . substr(md5($sId), 0, 4),
                    'type' => 'menu',
                    'is_visible' => false,
                ]);
                $stats['categories_created']++;
            }
            $categoryMap[$sId] = $category->id;
        }

        // Process Products
        foreach ($productsData as $prodItem) {
            $sId = $prodItem['_id'] ?? $prodItem['productId'] ?? null;
            if (!$sId) continue;

            $name = $prodItem['name'] ?? 'Unknown Product';
            $price = $prodItem['price'] ?? 0;
            $vat = $prodItem['vatRate'] ?? $prodItem['vat'] ?? 0;
            $sCatId = $prodItem['categoryId'] ?? null;

            $localCatId = $sCatId && isset($categoryMap[$sCatId]) ? $categoryMap[$sCatId] : null;

            $product = \App\Models\Product::where('storyous_id', $sId)->first();

            if ($product) {
                // Update existing product
                $product->update([
                    'name' => $name,
                    'price' => $price,
                    'vat_rate' => $vat,
                    'category_id' => $localCatId,
                ]);
                $stats['products_updated']++;
            } else {
                // Check for name conflict with local product (no storyous_id)
                $existingByName = \App\Models\Product::where('name', $name)->whereNull('storyous_id')->first();
                if ($existingByName) {
                    $existingByName->update(['name' => $name . ' - old']);
                    $stats['products_renamed_old']++;
                }

                // Create new product
                \App\Models\Product::create([
                    'storyous_id' => $sId,
                    'name' => $name,
                    'description' => $prodItem['notes'] ?? null,
                    'price' => $price,
                    'vat_rate' => $vat,
                    'category_id' => $localCatId,
                    'is_available' => false, // Hidden/Unavailable by default until reviewed
                ]);
                $stats['products_created']++;
            }
        }

        return ['status' => 'success', 'stats' => $stats];
    }

    /**
     * Synchronize bills from Storyous for a given period.
     * Defaults to settings start date -> now.
     */
    public function syncBills(?Carbon $fromDate = null): array
    {
        $startDateStr = $this->settings->sync_start_date;
        if (!$startDateStr && !$fromDate) {
            return ['status' => 'error', 'message' => 'Start date not configured'];
        }

        $start = $fromDate ? $fromDate->copy() : Carbon::parse($startDateStr);
        $end = now();

        $stats = ['processed_days' => 0, 'bills_created' => 0, 'bills_updated' => 0, 'errors' => 0];

        // Loop through days
        for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
            try {
                // Fetch bills list (summary)
                $bills = $this->getBillsForDate($date);

                foreach ($bills as $billData) {
                    $this->processBillSync($billData, $stats);
                }
                $stats['processed_days']++;

            } catch (\Exception $e) {
                Log::error("Sync error for date {$date->toDateString()}: " . $e->getMessage());
                $stats['errors']++;
            }
        }

        return ['status' => 'success', 'stats' => $stats];
    }

    protected function processBillSync(array $billData, array &$stats): void
    {
        $billId = $billData['billId'] ?? $billData['_id'] ?? null;
        if (!$billId) return;

        // Fetch full detail to get items
        $billDetail = $this->getBillDetail($billId);
        if (!$billDetail) {
            return;
        }
        $fullBillData = $billDetail['data'] ?? $billDetail;

        // Create/Update Bill
        $bill = \App\Models\Bill::updateOrCreate(
            ['storyous_bill_id' => $billId],
            [
                'bill_number' => $fullBillData['billNumber'] ?? null,
                'paid_at' => isset($fullBillData['paidAt']) ? Carbon::parse($fullBillData['paidAt']) : now(),
                'total_amount' => $fullBillData['finalPrice'] ?? $fullBillData['totalAmount'] ?? 0,
                'currency' => $fullBillData['currency'] ?? 'CZK',
                'table_number' => $fullBillData['tableNumber'] ?? null,
                'person_count' => $fullBillData['personCount'] ?? 0,
                'raw_data' => $fullBillData,
            ]
        );

        if ($bill->wasRecentlyCreated) {
            $stats['bills_created']++;
        } else {
            $stats['bills_updated']++;
        }

        // Process Items: Replace all items to ensure sync
        $bill->items()->delete();

        $items = $fullBillData['items'] ?? [];
        foreach ($items as $item) {
            $prodId = $item['productId'] ?? null;
            $localProduct = $prodId ? \App\Models\Product::where('storyous_id', $prodId)->first() : null;

            $amount = $item['amount'] ?? 1;
            $unitPrice = $item['price'] ?? 0;
            // Assuming price is per unit. If total, logic changes.
            // Standard POS API: price is usually unit price.

            $bill->items()->create([
                'product_id' => $localProduct?->id,
                'storyous_product_id' => $prodId,
                'name' => $item['name'] ?? 'Unknown',
                'quantity' => $amount,
                'price_per_unit' => $unitPrice,
                'total_price' => $amount * $unitPrice,
                'vat_rate' => $item['vatRate'] ?? 0,
            ]);
        }
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
