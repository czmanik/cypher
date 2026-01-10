<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class PlannedShiftsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('planned_shifts')->delete();
        
        \DB::table('planned_shifts')->insert(array (
            0 => 
            array (
                'id' => 1,
                'user_id' => 4,
                'start_at' => '2026-01-11 12:00:00',
                'end_at' => '2026-01-11 21:00:00',
                'shift_role' => 'kitchen',
                'color' => NULL,
                'note' => NULL,
                'is_published' => 1,
                'created_at' => '2026-01-10 16:58:08',
                'updated_at' => '2026-01-10 16:58:08',
                'status' => 'pending',
                'employee_comment' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'user_id' => 4,
                'start_at' => '2026-01-14 12:00:00',
                'end_at' => '2026-01-14 21:00:00',
                'shift_role' => 'kitchen',
                'color' => NULL,
                'note' => NULL,
                'is_published' => 1,
                'created_at' => '2026-01-10 16:58:08',
                'updated_at' => '2026-01-10 16:58:08',
                'status' => 'pending',
                'employee_comment' => NULL,
            ),
            2 => 
            array (
                'id' => 3,
                'user_id' => 4,
                'start_at' => '2026-01-15 12:00:00',
                'end_at' => '2026-01-15 21:00:00',
                'shift_role' => 'kitchen',
                'color' => NULL,
                'note' => NULL,
                'is_published' => 1,
                'created_at' => '2026-01-10 16:58:08',
                'updated_at' => '2026-01-10 16:58:08',
                'status' => 'pending',
                'employee_comment' => NULL,
            ),
            3 => 
            array (
                'id' => 4,
                'user_id' => 4,
                'start_at' => '2026-01-17 12:00:00',
                'end_at' => '2026-01-17 21:00:00',
                'shift_role' => 'kitchen',
                'color' => NULL,
                'note' => NULL,
                'is_published' => 1,
                'created_at' => '2026-01-10 16:58:08',
                'updated_at' => '2026-01-10 16:58:08',
                'status' => 'pending',
                'employee_comment' => NULL,
            ),
            4 => 
            array (
                'id' => 5,
                'user_id' => 4,
                'start_at' => '2026-01-18 12:00:00',
                'end_at' => '2026-01-18 21:00:00',
                'shift_role' => 'kitchen',
                'color' => NULL,
                'note' => NULL,
                'is_published' => 1,
                'created_at' => '2026-01-10 16:58:08',
                'updated_at' => '2026-01-10 16:58:08',
                'status' => 'pending',
                'employee_comment' => NULL,
            ),
            5 => 
            array (
                'id' => 6,
                'user_id' => 4,
                'start_at' => '2026-01-19 12:00:00',
                'end_at' => '2026-01-19 21:00:00',
                'shift_role' => 'kitchen',
                'color' => NULL,
                'note' => NULL,
                'is_published' => 1,
                'created_at' => '2026-01-10 16:58:08',
                'updated_at' => '2026-01-10 16:58:08',
                'status' => 'pending',
                'employee_comment' => NULL,
            ),
            6 => 
            array (
                'id' => 7,
                'user_id' => 4,
                'start_at' => '2026-01-20 12:00:00',
                'end_at' => '2026-01-20 21:00:00',
                'shift_role' => 'kitchen',
                'color' => NULL,
                'note' => NULL,
                'is_published' => 1,
                'created_at' => '2026-01-10 16:58:08',
                'updated_at' => '2026-01-10 16:58:08',
                'status' => 'pending',
                'employee_comment' => NULL,
            ),
            7 => 
            array (
                'id' => 8,
                'user_id' => 5,
                'start_at' => '2026-01-12 11:00:00',
                'end_at' => '2026-01-12 21:00:00',
                'shift_role' => 'kitchen',
                'color' => NULL,
                'note' => NULL,
                'is_published' => 1,
                'created_at' => '2026-01-10 18:51:06',
                'updated_at' => '2026-01-10 18:51:06',
                'status' => 'pending',
                'employee_comment' => NULL,
            ),
            8 => 
            array (
                'id' => 9,
                'user_id' => 5,
                'start_at' => '2026-01-13 11:00:00',
                'end_at' => '2026-01-13 21:00:00',
                'shift_role' => 'kitchen',
                'color' => NULL,
                'note' => NULL,
                'is_published' => 1,
                'created_at' => '2026-01-10 18:51:06',
                'updated_at' => '2026-01-10 18:51:06',
                'status' => 'pending',
                'employee_comment' => NULL,
            ),
        ));
        
        
    }
}