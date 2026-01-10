<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class TablesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('tables')->delete();
        
        \DB::table('tables')->insert(array (
            0 => 
            array (
                'id' => 1,
            'name' => 'Stůl 1 (Velký u zdi)',
                'capacity' => 5,
                'location' => 'indoor',
                'is_active' => 1,
                'created_at' => '2026-01-07 14:16:59',
                'updated_at' => '2026-01-07 14:16:59',
            ),
            1 => 
            array (
                'id' => 2,
            'name' => 'Stůl 2 (Velký zeď)',
                'capacity' => 5,
                'location' => 'indoor',
                'is_active' => 1,
                'created_at' => '2026-01-07 14:16:59',
                'updated_at' => '2026-01-07 14:18:36',
            ),
            2 => 
            array (
                'id' => 3,
                'name' => 'Stůl 3',
                'capacity' => 3,
                'location' => 'indoor',
                'is_active' => 1,
                'created_at' => '2026-01-07 14:16:59',
                'updated_at' => '2026-01-07 14:18:55',
            ),
            3 => 
            array (
                'id' => 4,
                'name' => 'Stůl 4',
                'capacity' => 3,
                'location' => 'indoor',
                'is_active' => 1,
                'created_at' => '2026-01-07 14:16:59',
                'updated_at' => '2026-01-07 14:19:10',
            ),
            4 => 
            array (
                'id' => 5,
                'name' => 'Stůl 5',
                'capacity' => 3,
                'location' => 'indoor',
                'is_active' => 1,
                'created_at' => '2026-01-07 14:16:59',
                'updated_at' => '2026-01-07 14:19:24',
            ),
            5 => 
            array (
                'id' => 6,
                'name' => 'Zahrádka 1',
                'capacity' => 4,
                'location' => 'garden',
                'is_active' => 0,
                'created_at' => '2026-01-07 14:16:59',
                'updated_at' => '2026-01-07 14:16:59',
            ),
        ));
        
        
    }
}