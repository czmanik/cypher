<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class MenuItemsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('menu_items')->delete();
        
        \DB::table('menu_items')->insert(array (
            0 => 
            array (
                'id' => 1,
                'label' => 'Domů',
                'type' => 'route',
                'page_id' => NULL,
                'route_name' => 'home',
                'url' => NULL,
                'sort_order' => 0,
                'new_tab' => 0,
                'created_at' => '2026-01-10 16:37:13',
                'updated_at' => '2026-01-10 16:37:13',
            ),
            1 => 
            array (
                'id' => 2,
                'label' => 'Denní menu',
                'type' => 'route',
                'page_id' => NULL,
                'route_name' => 'menu',
                'url' => NULL,
                'sort_order' => 1,
                'new_tab' => 0,
                'created_at' => '2026-01-10 16:38:35',
                'updated_at' => '2026-01-10 16:38:35',
            ),
            2 => 
            array (
                'id' => 3,
                'label' => 'Akce',
                'type' => 'route',
                'page_id' => NULL,
                'route_name' => 'events.index',
                'url' => NULL,
                'sort_order' => 2,
                'new_tab' => 0,
                'created_at' => '2026-01-10 16:39:18',
                'updated_at' => '2026-01-10 16:39:18',
            ),
            3 => 
            array (
                'id' => 4,
                'label' => 'O nás',
                'type' => 'page',
                'page_id' => 1,
                'route_name' => NULL,
                'url' => NULL,
                'sort_order' => 3,
                'new_tab' => 0,
                'created_at' => '2026-01-10 16:39:54',
                'updated_at' => '2026-01-10 16:39:54',
            ),
        ));
        
        
    }
}