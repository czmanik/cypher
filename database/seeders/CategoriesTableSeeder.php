<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CategoriesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('categories')->delete();
        
        \DB::table('categories')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'Burgery',
                'slug' => 'burgery',
                'type' => 'menu',
                'is_visible' => 1,
                'sort_order' => 10,
                'created_at' => '2026-01-07 14:14:21',
                'updated_at' => '2026-01-07 14:14:21',
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'Přílohy & Omáčky',
                'slug' => 'prilohy-omacky',
                'type' => 'menu',
                'is_visible' => 1,
                'sort_order' => 15,
                'created_at' => '2026-01-07 14:14:21',
                'updated_at' => '2026-01-07 14:14:21',
            ),
            2 => 
            array (
                'id' => 3,
                'name' => 'Káva',
                'slug' => 'kava',
                'type' => 'menu',
                'is_visible' => 1,
                'sort_order' => 20,
                'created_at' => '2026-01-07 14:14:21',
                'updated_at' => '2026-01-07 14:14:21',
            ),
            3 => 
            array (
                'id' => 4,
                'name' => 'Čaj',
                'slug' => 'caj',
                'type' => 'menu',
                'is_visible' => 1,
                'sort_order' => 25,
                'created_at' => '2026-01-07 14:14:21',
                'updated_at' => '2026-01-07 14:14:21',
            ),
            4 => 
            array (
                'id' => 5,
                'name' => 'Limonády',
                'slug' => 'limonady',
                'type' => 'menu',
                'is_visible' => 1,
                'sort_order' => 30,
                'created_at' => '2026-01-07 14:14:21',
                'updated_at' => '2026-01-07 14:14:21',
            ),
            5 => 
            array (
                'id' => 6,
                'name' => 'Nealko koktejly',
                'slug' => 'nealko-koktejly',
                'type' => 'menu',
                'is_visible' => 1,
                'sort_order' => 35,
                'created_at' => '2026-01-07 14:14:21',
                'updated_at' => '2026-01-07 14:14:21',
            ),
            6 => 
            array (
                'id' => 7,
                'name' => 'Pivo',
                'slug' => 'pivo',
                'type' => 'menu',
                'is_visible' => 1,
                'sort_order' => 40,
                'created_at' => '2026-01-07 14:14:21',
                'updated_at' => '2026-01-07 14:14:21',
            ),
            7 => 
            array (
                'id' => 8,
                'name' => 'Víno',
                'slug' => 'vino',
                'type' => 'menu',
                'is_visible' => 1,
                'sort_order' => 50,
                'created_at' => '2026-01-07 14:14:21',
                'updated_at' => '2026-01-07 14:14:21',
            ),
            8 => 
            array (
                'id' => 9,
                'name' => 'Koktejly',
                'slug' => 'koktejly',
                'type' => 'menu',
                'is_visible' => 1,
                'sort_order' => 60,
                'created_at' => '2026-01-07 14:14:21',
                'updated_at' => '2026-01-07 14:14:21',
            ),
            9 => 
            array (
                'id' => 10,
                'name' => 'Destiláty',
                'slug' => 'destilaty',
                'type' => 'menu',
                'is_visible' => 1,
                'sort_order' => 70,
                'created_at' => '2026-01-07 14:14:21',
                'updated_at' => '2026-01-07 14:14:21',
            ),
            10 => 
            array (
                'id' => 11,
                'name' => 'Merch',
                'slug' => 'merch',
                'type' => 'eshop',
                'is_visible' => 1,
                'sort_order' => 80,
                'created_at' => '2026-01-07 14:14:21',
                'updated_at' => '2026-01-07 14:14:21',
            ),
            11 => 
            array (
                'id' => 12,
                'name' => 'Zrnková káva',
                'slug' => 'zrnkova-kava',
                'type' => 'eshop',
                'is_visible' => 1,
                'sort_order' => 90,
                'created_at' => '2026-01-07 14:14:21',
                'updated_at' => '2026-01-07 14:14:21',
            ),
        ));
        
        
    }
}