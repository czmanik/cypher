<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ReservationsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('reservations')->delete();
        
        \DB::table('reservations')->insert(array (
            0 => 
            array (
                'id' => 1,
                'table_id' => NULL,
                'customer_name' => 'David',
                'customer_email' => 'test@test.cz',
                'customer_phone' => '736426519',
                'reservation_time' => '2026-01-09 18:12:00',
                'duration_minutes' => 90,
                'guests_count' => 5,
                'status' => 'pending',
                'note' => 'pokusný text, mám alergii na debily',
                'created_at' => '2026-01-08 15:12:45',
                'updated_at' => '2026-01-08 15:12:45',
            ),
        ));
        
        
    }
}