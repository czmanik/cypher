<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Načteme si kategorie, abychom měli jejich ID
        $catPivo = Category::where('slug', 'pivo')->first();
        $catKava = Category::where('slug', 'kava')->first();
        $catLimo = Category::where('slug', 'limonady')->first();
        $catBurgery = Category::where('slug', 'burgery')->first();

        // Pomocná funkce pro vložení, aby se nám neopakoval kód
        $createProduct = function ($category, $name, $price, $desc = null) {
            if ($category) {
                Product::create([
                    'category_id' => $category->id,
                    'name' => $name,
                    'price' => $price,
                    'description' => $desc,
                    'is_available' => true,
                    'is_shippable' => false, // Je to jídlo/pití na lístek
                ]);
            }
        };

        // --- PIVO ---
        if ($catPivo) {
            // Plzeň
            $createProduct($catPivo, 'Pilsner Urquell (Hladinka)', 69, '0,5l tankové');
            $createProduct($catPivo, 'Pilsner Urquell (Šnit / Malé)', 49, '0,3l tankové');
            
            // Radegast
            $createProduct($catPivo, 'Radegast 12° (Velké)', 65, '0,5l ze sudu');
            $createProduct($catPivo, 'Radegast 12° (Malé)', 45, '0,3l ze sudu');

            // Ratar
            $createProduct($catPivo, 'Radegast Ratar (Velké)', 69, '0,5l – extra hořké');
            $createProduct($catPivo, 'Radegast Ratar (Malé)', 49, '0,3l');

            // Kozel
            $createProduct($catPivo, 'Velkopopovický Kozel Černý (Velké)', 59, '0,5l');
            $createProduct($catPivo, 'Velkopopovický Kozel Černý (Malé)', 39, '0,3l');
        }

        // --- KÁVA ---
        if ($catKava) {
            $createProduct($catKava, 'Espresso', 55, '30ml, výběrová káva');
            $createProduct($catKava, 'Espresso Doppio', 75, '60ml, dvojitá dávka');
            $createProduct($catKava, 'Cappuccino', 65, '150ml, našlehané mléko');
            $createProduct($catKava, 'Flat White', 79, '180ml, dvojité espresso, mikropěna');
            $createProduct($catKava, 'Caffè Latte', 75, '280ml');
            $createProduct($catKava, 'Batch Brew', 60, '250ml, filtrovaná káva dne');
        }

        // --- LIMONÁDY ---
        if ($catLimo) {
            $createProduct($catLimo, 'Domácí malinovka', 65, '0,4l, s čerstvým ovocem');
            $createProduct($catLimo, 'Zázvorová limonáda', 65, '0,4l, poctivý výluh');
            $createProduct($catLimo, 'Coca-Cola', 55, '0,33l sklo');
        }

        // --- BURGERY ---
        if ($catBurgery) {
            $createProduct($catBurgery, 'Cypher Classic', 245, 'Hovězí maso, čedar, slanina, cibulové chutney, naše bulka');
            $createProduct($catBurgery, 'Jalapeño Smash', 255, '2x smashed maso, čedar, jalapeños, chipotle majonéza');
            $createProduct($catBurgery, 'Halloumi Veggie', 235, 'Grilovaný sýr Halloumi, pečená paprika, rukola');
        }
    }
}