<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class WorkShiftsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('work_shifts')->delete();
        
        \DB::table('work_shifts')->insert(array (
            0 => 
            array (
                'id' => 1,
                'user_id' => 3,
                'start_at' => '2026-01-09 11:00:00',
                'end_at' => '2026-01-09 23:00:00',
                'status' => 'approved',
                'total_hours' => 12,
                'calculated_wage' => 1500,
                'general_note' => NULL,
                'manager_note' => NULL,
                'created_at' => '2026-01-09 14:35:55',
                'updated_at' => '2026-01-10 15:34:53',
            ),
            1 => 
            array (
                'id' => 2,
                'user_id' => 4,
                'start_at' => '2026-01-09 13:00:00',
                'end_at' => '2026-01-09 21:27:37',
                'status' => 'approved',
                'total_hours' => 8.46,
                'calculated_wage' => 1500,
                'general_note' => NULL,
                'manager_note' => NULL,
                'created_at' => '2026-01-09 15:32:04',
                'updated_at' => '2026-01-09 21:28:14',
            ),
            2 => 
            array (
                'id' => 3,
                'user_id' => 5,
                'start_at' => '2026-01-09 14:00:00',
                'end_at' => '2026-01-09 21:25:56',
                'status' => 'approved',
                'total_hours' => 7.43,
                'calculated_wage' => 1337.4,
                'general_note' => NULL,
                'manager_note' => NULL,
                'created_at' => '2026-01-09 15:33:22',
                'updated_at' => '2026-01-10 18:23:49',
            ),
            3 => 
            array (
                'id' => 4,
                'user_id' => 5,
                'start_at' => '2026-01-08 11:00:00',
                'end_at' => '2026-01-08 21:00:00',
                'status' => 'approved',
                'total_hours' => 10,
                'calculated_wage' => 1800,
                'general_note' => NULL,
                'manager_note' => NULL,
                'created_at' => '2026-01-09 15:38:38',
                'updated_at' => '2026-01-09 15:39:10',
            ),
            4 => 
            array (
                'id' => 5,
                'user_id' => 5,
                'start_at' => '2026-01-10 14:30:00',
                'end_at' => NULL,
                'status' => 'active',
                'total_hours' => 0,
                'calculated_wage' => 0,
                'general_note' => NULL,
                'manager_note' => NULL,
                'created_at' => '2026-01-10 15:36:35',
                'updated_at' => '2026-01-10 15:36:35',
            ),
            5 => 
            array (
                'id' => 6,
                'user_id' => 3,
                'start_at' => '2026-01-10 13:00:00',
                'end_at' => NULL,
                'status' => 'active',
                'total_hours' => 0,
                'calculated_wage' => 0,
                'general_note' => NULL,
                'manager_note' => NULL,
                'created_at' => '2026-01-10 18:46:58',
                'updated_at' => '2026-01-10 18:46:58',
            ),
        ));
        
        
    }
}