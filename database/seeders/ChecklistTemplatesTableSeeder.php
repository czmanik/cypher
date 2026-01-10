<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ChecklistTemplatesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('checklist_templates')->delete();
        
        \DB::table('checklist_templates')->insert(array (
            0 => 
            array (
                'id' => 1,
                'task_name' => 'úklid - běžný',
                'is_required' => 1,
                'sort_order' => 0,
                'is_active' => 1,
                'created_at' => '2026-01-09 15:34:17',
                'updated_at' => '2026-01-09 15:34:17',
                'target_type' => 'all',
                'target_employee_type' => NULL,
                'target_user_id' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'task_name' => 'Uzávěrka kasy',
                'is_required' => 1,
                'sort_order' => 0,
                'is_active' => 1,
                'created_at' => '2026-01-09 15:34:53',
                'updated_at' => '2026-01-09 15:34:53',
                'target_type' => 'type',
                'target_employee_type' => 'manager',
                'target_user_id' => NULL,
            ),
            2 => 
            array (
                'id' => 3,
                'task_name' => 'kontrola toalet',
                'is_required' => 1,
                'sort_order' => 0,
                'is_active' => 1,
                'created_at' => '2026-01-09 15:35:17',
                'updated_at' => '2026-01-09 15:35:17',
                'target_type' => 'all',
                'target_employee_type' => NULL,
                'target_user_id' => NULL,
            ),
        ));
        
        
    }
}