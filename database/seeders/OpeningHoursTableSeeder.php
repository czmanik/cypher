<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class OpeningHoursTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('opening_hours')->delete();
        
        \DB::table('opening_hours')->insert(array (
            0 => 
            array (
                'id' => 1,
                'day_of_week' => 1,
                'bar_open' => '11:00',
                'bar_close' => '22:00',
                'kitchen_open' => '12:00',
                'kitchen_close' => '21:00',
                'is_closed' => 0,
                'created_at' => '2026-01-07 14:42:14',
                'updated_at' => '2026-01-09 16:02:18',
            ),
            1 => 
            array (
                'id' => 2,
                'day_of_week' => 2,
                'bar_open' => '08:00',
                'bar_close' => '23:59',
                'kitchen_open' => '11:00',
                'kitchen_close' => '22:00',
                'is_closed' => 0,
                'created_at' => '2026-01-07 14:42:14',
                'updated_at' => '2026-01-07 14:42:14',
            ),
            2 => 
            array (
                'id' => 3,
                'day_of_week' => 3,
                'bar_open' => '08:00',
                'bar_close' => '23:59',
                'kitchen_open' => '11:00',
                'kitchen_close' => '22:00',
                'is_closed' => 0,
                'created_at' => '2026-01-07 14:42:14',
                'updated_at' => '2026-01-07 14:42:14',
            ),
            3 => 
            array (
                'id' => 4,
                'day_of_week' => 4,
                'bar_open' => '08:00',
                'bar_close' => '23:59',
                'kitchen_open' => '11:00',
                'kitchen_close' => '22:00',
                'is_closed' => 0,
                'created_at' => '2026-01-07 14:42:14',
                'updated_at' => '2026-01-07 14:42:14',
            ),
            4 => 
            array (
                'id' => 5,
                'day_of_week' => 5,
                'bar_open' => '12:00',
                'bar_close' => '23:59',
                'kitchen_open' => '12:00',
                'kitchen_close' => '18:00',
                'is_closed' => 0,
                'created_at' => '2026-01-07 14:42:14',
                'updated_at' => '2026-01-09 00:25:35',
            ),
            5 => 
            array (
                'id' => 6,
                'day_of_week' => 6,
                'bar_open' => '08:00',
                'bar_close' => '23:59',
                'kitchen_open' => '11:00',
                'kitchen_close' => '22:00',
                'is_closed' => 0,
                'created_at' => '2026-01-07 14:42:14',
                'updated_at' => '2026-01-07 14:42:14',
            ),
            6 => 
            array (
                'id' => 7,
                'day_of_week' => 7,
                'bar_open' => '08:00',
                'bar_close' => '23:59',
                'kitchen_open' => '11:00',
                'kitchen_close' => '22:00',
                'is_closed' => 0,
                'created_at' => '2026-01-07 14:42:14',
                'updated_at' => '2026-01-07 14:42:14',
            ),
        ));
        
        
    }
}