<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ProductsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('products')->delete();
        
        \DB::table('products')->insert(array (
            0 => 
            array (
                'id' => 1,
                'category_id' => 7,
            'name' => 'Pilsner Urquell (Hladinka)',
                'description' => '0,5l tankové',
                'price' => 69,
                'image_path' => NULL,
                'is_available' => 1,
                'stock_qty' => NULL,
                'is_shippable' => 0,
                'created_at' => '2026-01-07 14:14:22',
                'updated_at' => '2026-01-07 14:14:22',
            ),
            1 => 
            array (
                'id' => 2,
                'category_id' => 7,
            'name' => 'Pilsner Urquell (Šnit / Malé)',
                'description' => '0,3l tankové',
                'price' => 49,
                'image_path' => NULL,
                'is_available' => 1,
                'stock_qty' => NULL,
                'is_shippable' => 0,
                'created_at' => '2026-01-07 14:14:22',
                'updated_at' => '2026-01-07 14:14:22',
            ),
            2 => 
            array (
                'id' => 3,
                'category_id' => 7,
            'name' => 'Radegast 12° (Velké)',
                'description' => '0,5l ze sudu',
                'price' => 65,
                'image_path' => NULL,
                'is_available' => 1,
                'stock_qty' => NULL,
                'is_shippable' => 0,
                'created_at' => '2026-01-07 14:14:22',
                'updated_at' => '2026-01-07 14:14:22',
            ),
            3 => 
            array (
                'id' => 4,
                'category_id' => 7,
            'name' => 'Radegast 12° (Malé)',
                'description' => '0,3l ze sudu',
                'price' => 45,
                'image_path' => NULL,
                'is_available' => 1,
                'stock_qty' => NULL,
                'is_shippable' => 0,
                'created_at' => '2026-01-07 14:14:22',
                'updated_at' => '2026-01-07 14:14:22',
            ),
            4 => 
            array (
                'id' => 5,
                'category_id' => 7,
            'name' => 'Radegast Ratar (Velké)',
                'description' => '0,5l – extra hořké',
                'price' => 69,
                'image_path' => NULL,
                'is_available' => 1,
                'stock_qty' => NULL,
                'is_shippable' => 0,
                'created_at' => '2026-01-07 14:14:22',
                'updated_at' => '2026-01-07 14:14:22',
            ),
            5 => 
            array (
                'id' => 6,
                'category_id' => 7,
            'name' => 'Radegast Ratar (Malé)',
                'description' => '0,3l',
                'price' => 49,
                'image_path' => NULL,
                'is_available' => 1,
                'stock_qty' => NULL,
                'is_shippable' => 0,
                'created_at' => '2026-01-07 14:14:22',
                'updated_at' => '2026-01-07 14:14:22',
            ),
            6 => 
            array (
                'id' => 7,
                'category_id' => 7,
            'name' => 'Velkopopovický Kozel Černý (Velké)',
                'description' => '0,5l',
                'price' => 59,
                'image_path' => NULL,
                'is_available' => 1,
                'stock_qty' => NULL,
                'is_shippable' => 0,
                'created_at' => '2026-01-07 14:14:22',
                'updated_at' => '2026-01-07 14:14:22',
            ),
            7 => 
            array (
                'id' => 8,
                'category_id' => 7,
            'name' => 'Velkopopovický Kozel Černý (Malé)',
                'description' => '0,3l',
                'price' => 39,
                'image_path' => NULL,
                'is_available' => 1,
                'stock_qty' => NULL,
                'is_shippable' => 0,
                'created_at' => '2026-01-07 14:14:22',
                'updated_at' => '2026-01-07 14:14:22',
            ),
            8 => 
            array (
                'id' => 9,
                'category_id' => 3,
                'name' => 'Espresso',
                'description' => '30ml, výběrová káva',
                'price' => 55,
                'image_path' => NULL,
                'is_available' => 1,
                'stock_qty' => NULL,
                'is_shippable' => 0,
                'created_at' => '2026-01-07 14:14:22',
                'updated_at' => '2026-01-07 14:14:22',
            ),
            9 => 
            array (
                'id' => 10,
                'category_id' => 3,
                'name' => 'Espresso Doppio',
                'description' => '60ml, dvojitá dávka',
                'price' => 75,
                'image_path' => NULL,
                'is_available' => 1,
                'stock_qty' => NULL,
                'is_shippable' => 0,
                'created_at' => '2026-01-07 14:14:22',
                'updated_at' => '2026-01-07 14:14:22',
            ),
            10 => 
            array (
                'id' => 11,
                'category_id' => 3,
                'name' => 'Cappuccino',
                'description' => '150ml, našlehané mléko',
                'price' => 65,
                'image_path' => NULL,
                'is_available' => 1,
                'stock_qty' => NULL,
                'is_shippable' => 0,
                'created_at' => '2026-01-07 14:14:22',
                'updated_at' => '2026-01-07 14:14:22',
            ),
            11 => 
            array (
                'id' => 12,
                'category_id' => 3,
                'name' => 'Flat White',
                'description' => '180ml, dvojité espresso, mikropěna',
                'price' => 79,
                'image_path' => NULL,
                'is_available' => 1,
                'stock_qty' => NULL,
                'is_shippable' => 0,
                'created_at' => '2026-01-07 14:14:22',
                'updated_at' => '2026-01-07 14:14:22',
            ),
            12 => 
            array (
                'id' => 13,
                'category_id' => 3,
                'name' => 'Caffè Latte',
                'description' => '280ml',
                'price' => 75,
                'image_path' => NULL,
                'is_available' => 1,
                'stock_qty' => NULL,
                'is_shippable' => 0,
                'created_at' => '2026-01-07 14:14:22',
                'updated_at' => '2026-01-07 14:14:22',
            ),
            13 => 
            array (
                'id' => 14,
                'category_id' => 3,
                'name' => 'Batch Brew',
                'description' => '250ml, filtrovaná káva dne',
                'price' => 60,
                'image_path' => NULL,
                'is_available' => 1,
                'stock_qty' => NULL,
                'is_shippable' => 0,
                'created_at' => '2026-01-07 14:14:22',
                'updated_at' => '2026-01-07 14:14:22',
            ),
            14 => 
            array (
                'id' => 15,
                'category_id' => 5,
                'name' => 'Domácí malinovka',
                'description' => '0,4l, s čerstvým ovocem',
                'price' => 65,
                'image_path' => NULL,
                'is_available' => 1,
                'stock_qty' => NULL,
                'is_shippable' => 0,
                'created_at' => '2026-01-07 14:14:22',
                'updated_at' => '2026-01-07 14:14:22',
            ),
            15 => 
            array (
                'id' => 16,
                'category_id' => 5,
                'name' => 'Zázvorová limonáda',
                'description' => '0,4l, poctivý výluh',
                'price' => 65,
                'image_path' => NULL,
                'is_available' => 1,
                'stock_qty' => NULL,
                'is_shippable' => 0,
                'created_at' => '2026-01-07 14:14:22',
                'updated_at' => '2026-01-07 14:14:22',
            ),
            16 => 
            array (
                'id' => 17,
                'category_id' => 5,
                'name' => 'Coca-Cola',
                'description' => '0,33l sklo',
                'price' => 55,
                'image_path' => NULL,
                'is_available' => 1,
                'stock_qty' => NULL,
                'is_shippable' => 0,
                'created_at' => '2026-01-07 14:14:22',
                'updated_at' => '2026-01-07 14:14:22',
            ),
            17 => 
            array (
                'id' => 18,
                'category_id' => 1,
                'name' => 'Cypher Classic',
                'description' => 'Hovězí maso, čedar, slanina, cibulové chutney, naše bulka',
                'price' => 245,
                'image_path' => NULL,
                'is_available' => 1,
                'stock_qty' => NULL,
                'is_shippable' => 0,
                'created_at' => '2026-01-07 14:14:22',
                'updated_at' => '2026-01-07 14:14:22',
            ),
            18 => 
            array (
                'id' => 19,
                'category_id' => 1,
                'name' => 'Jalapeño Smash',
                'description' => '2x smashed maso, čedar, jalapeños, chipotle majonéza',
                'price' => 255,
                'image_path' => NULL,
                'is_available' => 1,
                'stock_qty' => NULL,
                'is_shippable' => 0,
                'created_at' => '2026-01-07 14:14:22',
                'updated_at' => '2026-01-07 14:14:22',
            ),
            19 => 
            array (
                'id' => 20,
                'category_id' => 1,
                'name' => 'Halloumi Veggie',
                'description' => 'Grilovaný sýr Halloumi, pečená paprika, rukola',
                'price' => 235,
                'image_path' => NULL,
                'is_available' => 1,
                'stock_qty' => NULL,
                'is_shippable' => 0,
                'created_at' => '2026-01-07 14:14:22',
                'updated_at' => '2026-01-07 14:14:22',
            ),
        ));
        
        
    }
}